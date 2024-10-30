<?php

namespace Modules\Iprofile\Repositories\Cache;

use Modules\Iprofile\Repositories\InformationRepository;
use Modules\Core\Icrud\Repositories\Cache\BaseCacheCrudDecorator;

class CacheInformationDecorator extends BaseCacheCrudDecorator implements InformationRepository
{
    public function __construct(InformationRepository $information)
    {
        parent::__construct();
        $this->entityName = 'iprofile.information';
        $this->repository = $information;
    }
}
