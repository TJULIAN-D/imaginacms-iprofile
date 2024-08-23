<?php

namespace Modules\Iprofile\Http\Controllers\Api;

use Modules\Core\Icrud\Controllers\BaseCrudController;
//Model
use Modules\Iprofile\Entities\Setting;
use Modules\Iprofile\Repositories\SettingRepository;

class SettingApiController extends BaseCrudController
{
  public $model;
  public $modelRepository;

  public function __construct(Setting $model, SettingRepository $modelRepository)
  {
    $this->model = $model;
    $this->modelRepository = $modelRepository;
  }
}
