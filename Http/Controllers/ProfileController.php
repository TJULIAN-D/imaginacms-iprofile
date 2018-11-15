<?php

namespace Modules\Iprofile\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Http\Response;

use Mockery\CountValidator\Exception;

use Modules\User\Contracts\Authentication;

use Modules\Iprofile\Entities\Profile;

use Modules\Iprofile\Http\Requests\CreateProfileRequest;

use Modules\Iprofile\Http\Requests\UpdateProfileRequest;

use Modules\Iprofile\Repositories\ProfileRepository;

use Modules\Core\Http\Controllers\Admin\AdminBaseController;

use Modules\Setting\Contracts\Setting;

use Log;

use Modules\Iprofile\Repositories\AddressRepository;

use Modules\Iprofile\Transformers\AddressesTransformer;

use Modules\User\Repositories\UserRepository;

class ProfileController extends AdminBaseController

{

    /**
     * @var ProfileRepository
     */

    private $profile;
    private $auth;
    private $user;
    private $addresses;

    public function __construct(
      ProfileRepository $profile,
      Authentication $auth,
      UserRepository $user,
      AddressRepository $addresses)
    {
        parent::__construct();

        $this->profile = $profile;
        $this->auth = $auth;
        $this->user = $user;
        $this->addresses = $addresses;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    public function index()

    {

        $user = $this->auth->user();
        $profile = $this->profile->findByUserId($user->id);
        $addresses = $this->addresses->findByProfileId($profile->id);
        $addressesEncoded = json_encode(AddressesTransformer::collection($addresses));
         //Default Template
        $tpl = 'iprofile::frontend.index';
        $ttpl='iprofile.index';

        if(view()->exists($ttpl)) $tpl = $ttpl;

        return view($tpl, compact('user','profile','addressesEncoded','addresses'));

    }

    public function updateAddress(Request $request){
        $status=true;
        $data = $request->all();
        $user = $this->auth->user();
        $profile = $this->profile->findByUserId($user->id);
        $addresses=$this->addresses->updateMassiveById($data,$profile->id);
        $addressesEncoded = json_encode(AddressesTransformer::collection($addresses));
        return [
            "status" => $status,
            "addresses"=>$addresses,
            "addressesEncoded"=>$addressesEncoded
        ];
    }

    public function storeNewAddress(Request $request){
        try{
            $status=true;
            $data = $request->all();
            unset($data['id']);
            $user = $this->auth->user();
            $profile = $this->profile->findByUserId($user->id);
            $data[0]['profile_id']=$profile->id;
            if($data[0]['type']==null)
                $data[0]['type']="  ";
            $this->addresses->create($data[0]);
            $addresses = $this->addresses->findByProfileId($profile->id);
            $addressesEncoded = json_encode(AddressesTransformer::collection($addresses));
            return [
                "status" => $status,
                "addresses"=>$addresses,
                "addressesEncoded"=>$addressesEncoded
            ];
        }catch(\Exception $e){
            $status=false;
            return [
                "status" => $status,
                "error"=>$e->getMessage()
            ];
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    public function edit()

    {
        $user = $this->auth->user();
        $profile = $this->profile->findByUserId($user->id);
        $addresses = $this->addresses->findByProfileId($profile->id);
        $addressesEncoded = json_encode(AddressesTransformer::collection($addresses));
        $tpl = 'iprofile::frontend.edit';
        $ttpl='iprofile.edit';

        if(view()->exists($ttpl)) $tpl = $ttpl;
        return view($tpl , compact('user', 'profile','addressesEncoded','addresses'));

    }



    /**

     * Store a newly created resource in storage.
     *
     * @param  CreateProfileRequest $request
     * @return Response
     */

    public function store(CreateProfileRequest $request)

    {
        $user = $this->auth->user();
        $profile = $this->profile->findByUserId($user->id);
        try {
            $profile = $this->profile->update($profile,$request->all());

            if (count($profile)) {
                if (!empty($request['mainimage']) && !empty($profile->id)) {
                    $request['mainimage'] = $this->saveImage($request['mainimage'], "assets/iprofiles/profile/" . $profile->user_id . ".jpg");
                } else {
                    $request['mainimage'] = 'modules/iprofile/img/default.jpg';
                }
            }
            $profile->options = ['mainimage' => $request->mainimage];
            $profile->options = ['education' => $request->education];
            $profile->save();

            return redirect()->route('account.profile.index')
                ->withSuccess(trans('core::core.messages.resource created', ['name' => trans('iprofile::profiles.title.profiles')]));
        } catch (\Throwable $t) {
            //var_dump($t);
            $response['status'] = 'error';
            $response['message'] = $t->getMessage();
            Log::error($t);
            return redirect()->route('account.profile.index')
                ->withError($response['message']);
        }



    }





    /**

     * Update the specified resource in storage.
     *
     * @param  Profile $profile
     * @param  UpdateProfileRequest $request
     * @return Response
     */

    public function update(Profile $profile, UpdateProfileRequest $request)
    {
      $locale = \LaravelLocalization::setLocale() ?: \App::getLocale();
        $data = $request->all();
        $data[$locale]['bio'] = strip_tags($data[$locale]['bio'], '<p>');
        try {
          $profile = $this->profile->update($profile, $data);
          if (isset($request->label)) {
            $label = $request->label;
            $desc = $request->desc;
            for ($i = 0; $i < count($label); $i++) {
              if ($label[$i] != "fa-share-alt" && $desc[$i] != "")
                $socialResult[$i] = array('label' => $label[$i], 'desc' => $desc[$i]);
            }
            $profile->social = json_encode($socialResult);
            $profile->save();
          }

          if (count($profile)) {
            if (!empty($request['mainimage']) && !empty($profile->id)) {
              $request['mainimage'] = $this->saveImage($request['mainimage'], "assets/iprofiles/profile/" . $profile->user_id . ".jpg");
            } else {
              $request['mainimage'] = 'modules/iprofile/img/default.jpg';
            }
          }
          $profile->options = ['mainimage' => $request->mainimage, 'education' => $request->education];
          $profile->save();
          $user = $this->auth->user();
          $this->user->update($user, $request->all());
          return redirect()->route('account.profile.index')
            ->withSuccess(trans('core::core.messages.resource updated', ['name' => trans('iprofile::profiles.title.profiles')]));
        } catch (\Throwable $t) {
          //var_dump($t);
          $response['status'] = 'error';
          $response['message'] = $t->getMessage();
          Log::error($t);
          return redirect()->route('account.profile.index')
            ->withError($response['message']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $value
     * @param  $destination_path
     * @return Response
     */

    public function saveImage($value, $destination_path)

    {
          $disk = "publicmedia";
          //Defined return.
          if (starts_with($value, 'http')) {
              $url = url('modules/bcrud/img/default.jpg');
              if ($value == $url) {
                  return 'modules/iprofile/img/default.jpg';
              } else {
                  if (empty(str_replace(url(''), "", $value))) {

                      return 'modules/iprofile/img/default.jpg';
                  }
                  str_replace(url(''), "", $value);
                  return str_replace(url(''), "", $value);
              }
          };

          // if a base64 was sent, store it in the db
          if (starts_with($value, 'data:image')) {
            // 0. Make the image
            $image = \Image::make($value);
            // resize and prevent possible upsizing

            $image->resize(config('asgard.iprofile.config.imagesize.width'), config('asgard.iprofile.config.imagesize.height'), function ($constraint) {
              $constraint->aspectRatio();
              $constraint->upsize();
            });

            // 2. Store the image on disk.
            \Storage::disk($disk)->put($destination_path, $image->stream('jpg', '80'));
            // Save Thumbs
            \Storage::disk($disk)->put(
              str_replace('.jpg', '_mediumThumb.jpg', $destination_path),
              $image->fit(config('asgard.iprofile.config.mediumthumbsize.width'), config('asgard.iprofile.config.mediumthumbsize.height'))->stream('jpg', '80')

            );

            \Storage::disk($disk)->put(
              str_replace('.jpg', '_smallThumb.jpg', $destination_path),
              $image->fit(config('asgard.iprofile.config.smallthumbsize.width'), config('asgard.iprofile.config.smallthumbsize.height'))->stream('jpg', '80')
            );

            // 3. Return the path
            return $destination_path;

        }

        // if the image was erased
        if ($value == null) {
            // delete the image from disk
            \Storage::disk($disk)->delete($destination_path);

            // set null in the database column
            return null;
        }
    }





    public function updateUser(UpdateProfileRequest $request)

    {
        $user = $this->auth->user();
        $this->user->update($user, $request->all());
        return redirect()->route('account.profile.index')
            ->withSuccess(trans('core::core.messages.resource updated', ['name' => trans('iprofile::profiles.title.profiles')]));
    }





}
