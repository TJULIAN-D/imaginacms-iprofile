<?php

namespace Modules\Iprofile\Repositories\Cache;

use Modules\Iprofile\Repositories\RoleApiRepository;
use Modules\Core\Icrud\Repositories\Cache\BaseCacheCrudDecorator;

class CacheRoleApiApiDecorator extends BaseCacheCrudDecorator implements RoleApiRepository
{
    public function __construct(RoleApiRepository $role)
    {
        parent::__construct();
        $this->entityName = 'iprofile.roles';
        $this->repository = $role;
    }
}
