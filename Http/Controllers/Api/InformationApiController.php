<?php

namespace Modules\Iprofile\Http\Controllers\Api;

use Modules\Core\Icrud\Controllers\BaseCrudController;
//Model
use Modules\Iprofile\Entities\Information;
use Modules\Iprofile\Repositories\InformationRepository;

class InformationApiController extends BaseCrudController
{
  public $model;
  public $modelRepository;

  public function __construct(Information $model, InformationRepository $modelRepository)
  {
    $this->model = $model;
    $this->modelRepository = $modelRepository;
  }
}
