<?php

namespace Modules\Iprofile\Repositories\Cache;

use Modules\Iprofile\Repositories\SkillRepository;
use Modules\Core\Icrud\Repositories\Cache\BaseCacheCrudDecorator;

class CacheSkillDecorator extends BaseCacheCrudDecorator implements SkillRepository
{
    public function __construct(SkillRepository $skill)
    {
        parent::__construct();
        $this->entityName = 'iprofile.skills';
        $this->repository = $skill;
    }
}
