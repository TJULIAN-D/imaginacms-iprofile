<?php

namespace Modules\Iprofile\Http\Controllers\Api;

use Modules\Core\Icrud\Controllers\BaseCrudController;
//Model
use Modules\Iprofile\Entities\Field;
use Modules\Iprofile\Repositories\FieldRepository;

class FieldApiController extends BaseCrudController
{
    public $model;
    public $modelRepository;

    public function __construct(Field $model, FieldRepository $modelRepository)
    {
        $this->model = $model;
        $this->modelRepository = $modelRepository;
    }
}