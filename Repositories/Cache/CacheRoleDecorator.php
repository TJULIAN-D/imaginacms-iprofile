<?php

namespace Modules\Iprofile\Repositories\Cache;

use Modules\Iprofile\Repositories\RoleRepository;
use Modules\Core\Icrud\Repositories\Cache\BaseCacheCrudDecorator;

class CacheRoleDecorator extends BaseCacheCrudDecorator implements RoleRepository
{
    public function __construct(RoleRepository $role)
    {
        parent::__construct();
        $this->entityName = 'iprofile.roles';
        $this->repository = $role;
    }
}
