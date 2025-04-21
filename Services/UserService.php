<?php

namespace Modules\Iprofile\Services;

use Modules\Iprofile\Entities\Setting as ProfileSetting;
use Illuminate\Support\Facades\Auth;

use Laravel\Passport\TokenRepository;

class UserService
{
  public function getUserWorkspace($user)
  {
    //default
    $workspace = 'iadmin';

    // Get role user
    foreach ($user->roles as $key => $rol) {
      $userRoleId = $rol->id;
      break;
    }

    // Search Workspace user in setting by role
    $resultQuery = ProfileSetting::where('entity_name', 'role')
      ->where('related_id', $userRoleId)
      ->where('name', 'workSpace')->first();

    if (!empty($resultQuery) && !is_null($resultQuery)) {
      $workspace = $resultQuery->value;
    }

    // return data
    return $workspace;
  }


  /**
   * Login with token | Example Case: Weigo v10
   */
  public function loginWithToken($authorizationHeader)
  {
    try {
      if (!$authorizationHeader || !str_starts_with($authorizationHeader, 'Bearer ')) {
        return null;
      }

      //Remove 'Bearer ' prefix
      $tokenString = substr($authorizationHeader, 7);
      $jwt = app('Lcobucci\JWT\Parser')->parse($tokenString);
      //Extract the token ID
      $tokenId = $jwt->claims()->get('jti');

      // Validate the token
      $tokenRepository = app(TokenRepository::class);
      $token = $tokenRepository->find($tokenId);

      if (!$token || $token->revoked || $token->expires_at <= now()) {
        return null;
      }

      // Get the user associated with the token
      $user = $token->user;
      if (!$user) {
        return null;
      }

      // Authenticate the user for the current request
      Auth::setUser($user);

      return $user;
    }catch (\Exception $e){
      return null;
    }
  }

}
