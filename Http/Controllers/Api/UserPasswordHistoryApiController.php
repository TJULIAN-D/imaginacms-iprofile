<?php

namespace Modules\Iprofile\Http\Controllers\Api;

use Modules\Core\Icrud\Controllers\BaseCrudController;
//Model
use Modules\Iprofile\Entities\UserPasswordHistory;
use Modules\Iprofile\Repositories\UserPasswordHistoryRepository;

class UserPasswordHistoryApiController extends BaseCrudController
{
  public $model;
  public $modelRepository;

  public function __construct(UserPasswordHistory $model, UserPasswordHistoryRepository $modelRepository)
  {
    $this->model = $model;
    $this->modelRepository = $modelRepository;
  }
}
