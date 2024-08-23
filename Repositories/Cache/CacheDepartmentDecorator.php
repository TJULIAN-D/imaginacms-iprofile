<?php

namespace Modules\Iprofile\Repositories\Cache;

use Modules\Iprofile\Repositories\DepartmentRepository;
use Modules\Core\Icrud\Repositories\Cache\BaseCacheCrudDecorator;

class CacheDepartmentDecorator extends BaseCacheCrudDecorator implements DepartmentRepository
{
    public function __construct(DepartmentRepository $department)
    {
        parent::__construct();
        $this->entityName = 'iprofile.departments';
        $this->repository = $department;
    }
}
