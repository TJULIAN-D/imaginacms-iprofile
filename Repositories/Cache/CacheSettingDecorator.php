<?php

namespace Modules\Iprofile\Repositories\Cache;

use Modules\Iprofile\Repositories\SettingRepository;
use Modules\Core\Icrud\Repositories\Cache\BaseCacheCrudDecorator;

class CacheSettingDecorator extends BaseCacheCrudDecorator implements SettingRepository
{
    public function __construct(SettingRepository $setting)
    {
        parent::__construct();
        $this->entityName = 'iprofile.settings';
        $this->repository = $setting;
    }
}
