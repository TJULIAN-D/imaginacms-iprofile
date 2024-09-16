<?php

namespace Modules\Iprofile\Repositories\Cache;

use Modules\Iprofile\Repositories\ProviderAccountRepository;
use Modules\Core\Icrud\Repositories\Cache\BaseCacheCrudDecorator;

class CacheProviderAccountDecorator extends BaseCacheCrudDecorator implements ProviderAccountRepository
{
    public function __construct(ProviderAccountRepository $provideraccount)
    {
        parent::__construct();
        $this->entityName = 'iprofile.provideraccounts';
        $this->repository = $provideraccount;
    }
}