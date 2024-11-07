<?php

namespace Modules\Iprofile\Http\Controllers\Api;

use Exception;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\Core\Http\Controllers\BasePublicController;
use Modules\Ihelpers\Http\Controllers\Api\BaseApiController;
use Modules\User\Entities\Sentinel\User;
use Modules\Iprofile\Events\ImpersonateEvent;
use Modules\Iprofile\Repositories\UserApiRepository;
use Modules\User\Exceptions\InvalidOrExpiredResetCode;
use Modules\User\Exceptions\UserNotFoundException;
use Modules\User\Http\Requests\ResetCompleteRequest;
use Modules\User\Http\Requests\ResetRequest;
use Modules\User\Services\UserResetter;
use Socialite;
use Modules\User\Contracts\Authentication;

// Reset

// Socialite
use Laravel\Socialite\Contracts\User as ProviderUser;
use Modules\Iprofile\Entities\ProviderAccount;
use Modules\User\Repositories\RoleRepository;
use Modules\User\Repositories\UserRepository;
use Modules\Iprofile\Http\Controllers\Api\FieldApiController;

//Controllers

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class AuthApiController extends BaseApiController
{
  private $providerAccount;

  private $role;

  private $userRepository;

  private $userApiController;

  private $fieldApiController;

  private $user;

  protected $auth;

  public function __construct(UserApiController $userApiController, FieldApiController $fieldApiController, UserApiRepository $user, UserRepository $userRepository)
  {
    parent::__construct();
    $this->userRepository = $userRepository;
    $this->userApiController = $userApiController;
    $this->fieldApiController = $fieldApiController;
    $this->user = $user;
    $this->auth = app(Authentication::class);
    $this->clearTokens();//CLear tokens
  }

  /**
   * Login Api Controller
   *
   * @return mixed
   */
  public function login(Request $request)
  {
    try {
      $credentials = [ //Get credentials
        'login' => $request->input('username'),
        'password' => $request->input('password'),
      ];

      //Auth attemp and get token
      $token = $this->validateResponseApi($this->authAttempt($credentials));
      $user = $this->validateResponseApi($this->me()); //Get user Data

      $response = ['data' => [
        'userToken' => $token->bearer,
        'expiresIn' => $token->expiresDate,
        'userData' => $user->userData,
      ]]; //Response
    } catch (Exception $e) {
      $status = $this->getStatusError($e->getCode());
      $response = ['errors' => $this->getErrorMessage($e)];
    }

    //Return response
    return response()->json($response ?? ['data' => 'Request successful'], $status ?? 200);
  }

  /**
   * Reset Api Controller
   *
   * @return mixed
   */
  public function reset(Request $request)
  {
    try {
      //Get data
      $data = (object)$request->input('attributes');

      $credentials = [ //Get credentials
        'email' => $data->username,
      ];
      app(UserResetter::class)->startReset($credentials);

      $response = ['data' => ['message' => trans('iprofile::cms.message.checkMail')]]; //Response
    } catch (UserNotFoundException $e) {
      $status = $this->getStatusError(404);
      $response = ['errors' => trans('user::messages.no user found')];
    } catch (Exception $e) {
      $status = $this->getStatusError($e->getCode());
      $response = ['errors' => $e->getMessage()];
    }

    //Return response
    return response()->json($response ?? ['data' => 'Request successful'], $status ?? 200);
  }

  /**
   * Reset Complete Api Controller
   *
   * @return mixed
   */
  public function resetComplete(Request $request)
  {
    try {
      $credentials = [ //Get credentials
        'password' => $request->input('password'),
        'password_confirmation' => $request->input('password_confirmation'),
        'userId' => $request->input('userId') ?? $request->input('user_id'),
        'code' => $request->input('code'),
      ];
      $this->validateRequestApi(new ResetCompleteRequest($credentials));
      app(UserResetter::class)->finishReset($credentials);

      $user = $this->user->find($credentials['userId']);

      $response = ['data' => ['email' => $user->email]]; //Response
    } catch (UserNotFoundException $e) {
      \Log::error($e->getMessage());
      $status = $this->getStatusError(404);
      $response = ['errors' => trans('user::messages.no user found')];
    } catch (InvalidOrExpiredResetCode $e) {
      $status = $this->getStatusError(402);
      $response = ['errors' => trans('user::messages.invalid reset code')];
    } catch (Exception $e) {
      $status = $this->getStatusError($e->getCode());
      $response = ['errors' => $e->getMessage()];
    }

    //Return response
    return response()->json($response ?? ['data' => 'Request successful'], $status ?? 200);
  }

  /**
   * Auth Attempt Api Controller
   *
   * @return mixed
   */
  public function authAttempt($credentials)
  {
    try {
      $credentials = (object)$credentials;

      try {
        //Find email in users fields
        $field = $this->validateResponseApi(
          $this->fieldApiController->show(
            json_encode($credentials->login),
            new Request([
              'filter' => json_encode(['field' => 'value']),
              'include' => 'user',
            ])
          )
        );

        //If exist email in users fields, change email of credentials
        if (isset($field->user)) {
          $credentials->login = $field->user->email;
        }
      } catch (Exception $e) {
      }

      $error = $this->auth->login((array)$credentials);

      //Try login
      if (!$error) {
        $user = $this->auth->user(); //Get user
        $token = $this->getToken($user); //Get token

        //Response bearer and expires date
        $response = ['data' => [
          'bearer' => 'Bearer ' . $token->accessToken,
          'expiresDate' => $token->token->expires_at,
        ]];
      } else {
        throw new Exception(is_string($error) ? $error : 'User or Password invalid', 400);
      }
    } catch (Exception $e) {
      /*
      $status = $this->getStatusError($e->getCode());
      $response = ["errors" => $this->getErrorMessage($e)];
      if ($e->getMessage() === 'Your account has not been activated yet.') $status = 400;
      */
      $status = $this->getStatusError($e->getCode());
      $response = ['errors' => $e->getMessage()];
    }

    //Return response
    return response()->json($response ?? ['data' => 'Request successful'], $status ?? 200);
  }

  /**
   * Me Api Controller
   *
   * @return mixed
   */
  public function me()
  {
    try {
      $user = Auth::user(); //Get user loged

      //add: custom user includes from config (slim)
      $customUserIncludes = config('asgard.iprofile.config.customUserIncludes');
      $includes = array_keys($customUserIncludes);

      //Find user with relationships
      $userData = $this->validateResponseApi(
        $this->userApiController->show($user->id, new Request([
            'include' => 'fields,departments,organizations,addresses,settings,roles' . (count($includes) ? ',' . implode(',', $includes) : '')]
        ))
      );

      if (is_module_enabled('Icommerce')) {
        // Get a collection with the configuration of each payout for the logged in user
        $payoutsConfigUser = app('Modules\Icommerce\Services\PaymentMethodService')->getPayoutsForUser();

        // Add in userData
        if (!is_null($payoutsConfigUser)) {
          $userData->payouts = $payoutsConfigUser;
        }
      }

      // Re-sync session with website
      $this->auth->logUserIn($user);

      //Response
      $response = ['data' => [
        'userData' => $userData,
      ]];
    } catch (Exception $e) {
      $status = $this->getStatusError($e->getCode());
      $response = ['errors' => $this->getErrorMessage($e)];
    }

    //Return response
    return response()->json($response ?? ['data' => 'Request successful'], $status ?? 200);
  }

  /**
   * Logout Api Controller
   *
   * @return mixed
   */
  public function logout(Request $request)
  {

    try {
      $token = $this->validateResponseApi($this->getRequestToken($request)); //Get Token
      if ($token) DB::table('oauth_access_tokens')->where('id', $token->id)->delete(); //Delete Token
      $this->auth->logout();
    } catch (Exception $e) {
      $status = $this->getStatusError($e->getCode());
      $response = ['errors' => $e->getMessage()];
    }

    //Return response
    return response()->json($response ?? ['data' => 'You have been successfully logged out!'], $status ?? 200);
  }

  /**
   * logout All Sessions Api Controller
   *
   * @return mixed
   */
  public function logoutAllSessions(Request $request)
  {
    try {
      $userId = $request->input('userId'); //Get user ID form request
      if (isset($userId)) {
        //Delete all tokens of this user
        DB::table('oauth_access_tokens')->where('user_id', $userId)->delete();
      }
    } catch (Exception $e) {
      $status = $this->getStatusError($e->getCode());
      $response = ['errors' => $e->getMessage()];
    }

    //Return response
    return response()->json($response ?? ['data' => 'You have been successfully logged out!'], $status ?? 200);
  }

  /**
   * Impersonate Api Controller
   *
   * @return mixed
   */
  public function impersonate(Request $request)
  {
    try {
      //Get Token
      $this->validateResponseApi($this->getRequestToken($request));

      $userId = $request->input('userId'); //GEt user id impersonator
      $userIdToImpersonate = $request->input('userIdImpersonate'); //Get user ID to impersonate

      Auth::loginUsingId($userId); //Loged impersonator
      $params = $this->getParamsRequest($request); //Get params

      //Check permissions of impersonator and settings to impersonate
      if (isset($params->permissions['profile.user.impersonate']) && $params->permissions['profile.user.impersonate']) {
        //Emit event impersonate
        event(new ImpersonateEvent($userIdToImpersonate, $request->ip()));

        Auth::logout(); //logout impersonator
        $userImpersonate = Auth::loginUsingId($userIdToImpersonate); //Loged impersonator
        $token = $this->getToken($userImpersonate); //Get Token
        $user = $this->validateResponseApi($this->me()); //Get user Data

        //Response
        $response = ['data' => [
          'userToken' => 'Bearer ' . $token->accessToken,
          'expiresIn' => $token->token->expires_at,
          'userData' => $user->userData,
        ]];
      } else {
        throw new Exception('Unauthorized', 403);
      }
    } catch (Exception $e) {
      $status = $this->getStatusError($e->getCode());
      $response = ['errors' => $e->getMessage()];
    }

    //Return response
    return response()->json($response ?? ['data' => 'Request successful'], $status ?? 200);
  }

  /**
   * Refresh Token Api Controller
   *
   * @return mixed
   */
  public function refreshToken(Request $request)
  {
    try {
      //Get Token
      $token = $this->validateResponseApi($this->getRequestToken($request));
      $expiresIn = now()->addMinutes(1440);

      //Add 15 minutos to token
      DB::table('oauth_access_tokens')->where('id', $token->id)->update([
        'updated_at' => now(),
        'expires_at' => $expiresIn,
      ]);

      $response = ['data' => ['expiresIn' => $expiresIn]];
    } catch (Exception $e) {
      $status = $this->getStatusError($e->getCode());
      $response = ['errors' => $e->getMessage()];
    }

    //Return response
    return response()->json($response ?? ['data' => 'Request successful'], $status ?? 200);
  }

  /**
   * GET A ITEM
   *
   * @return mixed
   */
  public function getSocialAuth(Request $request, $criteria)
  {
    try {
      if (!config("services.$criteria")) {
        throw new \Exception('Invalid social criteria', 401);
      }
      //Get Parameters from URL.
      $params = $this->getParamsRequest($request);
      //Get data
      $data = $request->input('attributes');

      $user = $this->_createOrGetUser($criteria, $data);

      //Request to Repository
      if (isset($user->id)) {
        $auth = \Sentinel::login($user);
        $token = $this->getToken($auth);//Get token
        $userData = $this->validateResponseApi($this->me());

        $response = ["data" => [
          'userToken' => 'Bearer ' . $token->accessToken,
          'expiresIn' => $token->token->expires_at,
          'userData' => $userData->userData
        ]];//Response
      } else {
        throw new \Exception('User not found', 404);
      }

    } catch (\Exception $e) {
      $status = $this->getStatusError($e->getCode());
      $response = ["errors" => $e->getMessage()];
    }

    //Return response
    return response()->json($response, $status ?? 200);
  }


  /**
   * GET SOCIAL
   *
   * @param
   * @return
   */
  function _createOrGetUser($criteria, $data)
  {

    if ($criteria == "facebook") {
      $fields = ['first_name', 'last_name', 'picture.width(1920).redirect(false)', 'email', 'gender', 'birthday', 'address', 'about', 'link'];
      $providerUser = Socialite::driver($criteria)->stateless()->fields($fields)->userFromToken($data["token"]);
    } else if ($criteria == "google") {
      $providerUser = Socialite::driver("google-jwt")->stateless()->userFromToken($data["token"]);
    } else
      $providerUser = Socialite::driver($criteria)->stateless()->userFromToken($data["token"]);

    $providerAccount = app('Modules\Iprofile\Entities\ProviderAccount');
    $provideraccount = $providerAccount->whereProvider($criteria)->whereProviderUserId($providerUser->getId())
      ->first();
    //dd($providerUser,$providerUser->getAvatar());
    //If user for this social login exists update short token and return the user associated
    if (isset($provideraccount->user)) {
      $updateoptions = $provideraccount->options;
      $updateoptions['short_token'] = $providerUser->token;
      $provideraccount->options = $updateoptions;
      $provideraccount->save();

      return $provideraccount->user;

      //New social login or user
    } else {
      $userdata = (object)[];

      $userdata->email = $providerUser->getEmail();
      $userdata->password = Str::random(16);

      if ($criteria == 'facebook') {
        //$social_picture = $providerUser->user['picture']['data'];
        $userdata->first_name = $providerUser->user['first_name'];
        $userdata->last_name = $providerUser->user['last_name'];
      } else {
        $fullname = explode(' ', $providerUser->getName());
        $userdata->first_name = $fullname[0];
        $userdata->last_name = $fullname[1];
      }

      $existUser = false;
      $user = User::where('email', $userdata->email)->first();

      if (!$user) {
        $whiteListEmails = config('asgard.iprofile.config.whiteListEmails');
        if (!empty($whiteListEmails)) {
          if (!in_array($userdata->email, $whiteListEmails)) {
            throw new \Exception('Email register unauthorized', 401);
          }
        }

        //Format dat ot create user
        $data = [
          'first_name' => $userdata->first_name,
          'last_name' => $userdata->last_name,
          'email' => $userdata->email,
          'password' => $userdata->password,
          'password_confirmation' => $userdata->password,
          'departments' => [1], //Default department is USERS, ID 1
          'roles' => [2], // role 2 User
          'activated' => true,
        ];

        //Create user
        $user = $this->userRepository->createWithRoles($data, $data['roles'], $data['activated']);

        if ($user) {
          $user->departments()->sync(Arr::get($data, 'departments', []));
          $user = User::where('email', $userdata->email)->first();
        } else {
          return null;
        }
      } else {
        $existUser = true;
      }

      if (isset($user->email) && !empty($user->email)) {
        $createSocial = true;
        if ($existUser) {
          $providerData = ProviderAccount::where('user_id', $user->id)->first();
          if ($providerData) {
            $createSocial = false;
          }
        }
        if ($createSocial) {
          //Let's associate the Social Login with this user
          $provideraccount = new ProviderAccount();
          $provideraccount->provider_user_id = $providerUser->getId();
          $provideraccount->user_id = $user->id;
          $provideraccount->provider = $criteria;
          $provideraccount->options = ['short_token' => $providerUser->token];
          $provideraccount->save();

          //Let's create the Profile for this user
          switch ($criteria) {
            case 'facebook':
              $social_picture = str_replace('type=normal', 'width=1920', $providerUser->getAvatar());
              break;
            case 'google':
              $social_picture = str_replace('=s50', '=s1920', $providerUser->getAvatar());
              break;
          }


          $b64image = 'data:image/jpg;base64,' . base64_encode(file_get_contents($social_picture));
          $field['user_id'] = $user->id;// Add user Id
          $field['value'] = $b64image;
          $field['name'] = 'mainImage';
          $this->validateResponseApi($this->fieldApiController->create(new Request(['attributes' => (array)$field])));

        }


      } else {
        return null;
      }

      return $user;
    }
  }

  /*======== Private Methods ========*/
  //Return token from request
  private function getRequestToken($request)
  {
    try {
      $bearerToken = $request->bearerToken(); //Get from request
      if ($bearerToken) {
        $this->user = \Auth::user() ?? null;
        $token = $this->user->token() ?? null;

        //Default state
        $success = true;

        //Validate if exist token
        if (!isset($token)) {
          $success = false;
        }

        //Validate if is revoked
        if (isset($token) && (int)$token->revoked >= 1) {
          $success = false;
        }

        //Validate if Token was ended
        if (isset($token) && (strtotime(now()) >= strtotime($token->expires_at))) {
          $success = false;
        }

        //Revoke Token if is invalid
        if (!$success) {
          if (isset($token)) {
            $token->delete();
          }//Delete token
        }

        $response = ['data' => $token]; //Response Token ID decode
        \DB::commit(); //Commit to DataBase
      } else {
        throw new Exception('Unauthorized', 401);
      }//Throw unautorize
    } catch (Exception $e) {
      $status = $this->getStatusError($e->getCode());
      $response = ['errors' => $e->getMessage()];
    }

    //Return response
    return response()->json($response, $status ?? 200);
  }

  /**
   * Provate method Clear Tokens
   */
  private function clearTokens()
  {
    //Delete all tokens expirateds or revoked
    DB::table('oauth_access_tokens')->where('expires_at', '<=', now())
      ->orWhere('revoked', 1)
      ->delete();
  }


  /**
   * @param $user
   * @return bool
   */
  private function getToken($user)
  {
    if (isset($user))
      return $user->createToken('Laravel Password Grant Client');
    else return false;
  }

  /**
   * Access With Email Api Controller
   * @param Request $request
   * @return mixed
   */
  public function accessWithEmail(Request $request)
  {
    \DB::beginTransaction();
    try {
      $data = $request->input('attributes') ?? [];

      $validator = Validator::make($data, [
        'email' => 'required|email'
      ]);

      if ($validator->fails()) {
        throw new Exception("Incorrect email", 400);
      }

      $user = User::where('email', $data["email"])->first();

      //Verify if exist user
      if (!$user) {
        $userData = [
          "email" => $data["email"],
          "password" => generatePassword()
        ];

        //Create user with email and possword
        $user = $this->userRepository->createWithRoles($userData, [2], true);
      }

      //Generate token, to use 1 time
      $userToken = $user->generateToken(1, 1);
      $url = route('auth.validate.email-token', ['token' => $userToken->token, 'redirectTo' => $data["redirectTo"]]);

      //Import notification
      $notification = app("Modules\Notification\Services\Inotification");
      $notification->provider('email')
        ->to($data["email"])
        ->push([
          "title" => trans("iprofile::frontend.email.readyToProceed"),
          "message" => trans("iprofile::frontend.email.clickLogin"),
          "buttonText" => trans("iprofile::frontend.email.proceedToLogin"),
          "withButton" => true,
          "link" => $url,
          "extraMessage" => trans("iprofile::frontend.email.loginLink", ['link' => $url]),
          "view" => "iprofile::emails.layouts.access-email"
        ]);

      \DB::commit();//Commit to DataBase
    } catch (Exception $e) {
      \DB::rollback();//Rollback to Data Base
      $status = $this->getStatusError($e->getCode());
      $response = ["errors" => $e->getMessage()];
    }

    //Return response
    return response()->json($response ?? ["data" => "Request successful"], $status ?? 200);
  }
}
