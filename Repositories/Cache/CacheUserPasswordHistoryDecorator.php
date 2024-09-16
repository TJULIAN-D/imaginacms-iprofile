<?php

namespace Modules\Iprofile\Repositories\Cache;

use Modules\Iprofile\Repositories\UserPasswordHistoryRepository;
use Modules\Core\Icrud\Repositories\Cache\BaseCacheCrudDecorator;

class CacheUserPasswordHistoryDecorator extends BaseCacheCrudDecorator implements UserPasswordHistoryRepository
{
    public function __construct(UserPasswordHistoryRepository $userpasswordhistory)
    {
        parent::__construct();
        $this->entityName = 'iprofile.userpasswordhistories';
        $this->repository = $userpasswordhistory;
    }
}