<?php

namespace Modules\Iprofile\Http\Controllers\Api;

use Modules\Core\Icrud\Controllers\BaseCrudController;
//Model
use Modules\Iprofile\Entities\Address;
use Modules\Iprofile\Repositories\AddressRepository;

class AddressApiController extends BaseCrudController
{
    public $model;
    public $modelRepository;

    public function __construct(Address $model, AddressRepository $modelRepository)
    {
        $this->model = $model;
        $this->modelRepository = $modelRepository;
    }
}