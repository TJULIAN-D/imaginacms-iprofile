<?php

namespace Modules\Iprofile\Http\Controllers\Api;

use Modules\Core\Icrud\Controllers\BaseCrudController;
//Model
use Modules\Iprofile\Entities\ProviderAccount;
use Modules\Iprofile\Repositories\ProviderAccountRepository;

class ProviderAccountApiController extends BaseCrudController
{
  public $model;
  public $modelRepository;

  public function __construct(ProviderAccount $model, ProviderAccountRepository $modelRepository)
  {
    $this->model = $model;
    $this->modelRepository = $modelRepository;
  }
}
