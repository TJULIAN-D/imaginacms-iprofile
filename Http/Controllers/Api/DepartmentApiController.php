<?php

namespace Modules\Iprofile\Http\Controllers\Api;

use Modules\Core\Icrud\Controllers\BaseCrudController;

//Model
use Modules\Iprofile\Entities\Department;
use Modules\Iprofile\Repositories\DepartmentRepository;

class DepartmentApiController extends BaseCrudController
{
  public $model;
  public $modelRepository;

  public function __construct(Department $model, DepartmentRepository $modelRepository)
  {
    $this->model = $model;
    $this->modelRepository = $modelRepository;
  }
}
