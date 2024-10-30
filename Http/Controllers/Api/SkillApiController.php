<?php

namespace Modules\Iprofile\Http\Controllers\Api;

use Modules\Core\Icrud\Controllers\BaseCrudController;
//Model
use Modules\Iprofile\Entities\Skill;
use Modules\Iprofile\Repositories\SkillRepository;

class SkillApiController extends BaseCrudController
{
  public $model;
  public $modelRepository;

  public function __construct(Skill $model, SkillRepository $modelRepository)
  {
    $this->model = $model;
    $this->modelRepository = $modelRepository;
  }
}
