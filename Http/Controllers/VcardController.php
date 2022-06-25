<?php

namespace Modules\Iprofile\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Log;
use Mockery\CountValidator\Exception;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\Setting\Contracts\Setting;

//**** Iprofile
use Modules\Iprofile\Repositories\UserApiRepository;

//**** User
use Modules\User\Repositories\UserRepository;
use Modules\User\Contracts\Authentication;

//**** Vcard
use JeroenDesloovere\VCard\VCard;

class VcardController extends AdminBaseController
{

  private $auth;
  private $user;
  private $userApi;

  public function __construct(

    Authentication    $auth,
    UserRepository    $user,
    UserApiRepository $userApi

  )
  {
    parent::__construct();

    $this->auth = $auth;
    $this->user = $user;
    $this->userApi = $userApi;

  }

  public function create($userID)
  {

    $user = $this->userApi->getItem($userID);

    $vcard = new VCard();

    // add personal data
    $vcard->addName($user->lastName, $user->firstName,'','','');

    // add work data
    $vcard->addCompany(setting("core::site-name"));
    $vcard->addJobtitle($user->settings->where("name","jobTitle")->first()->value ?? "");
    $vcard->addRole($user->settings->where("name","jobRole")->first()->value ?? "");
    $vcard->addEmail($user->settings->where("name","jobEmail")->first()->value ?? $user->email);
    $vcard->addPhoneNumber($user->settings->where("name","jobMobile")->first()->value ?? "", 'PREF;WORK');

    $defaultImage = \URL::to('/modules/iprofile/img/default.jpg');
    $mainImage = $user->fields ? $user->fields->where('name', 'mainImage')->first() : null;
    $vcard->addPhoto(url('/'.isset($mainImage->value) ? $mainImage->value : $defaultImage));

    // return vcard as a download
    return $vcard->download();
  }

}
