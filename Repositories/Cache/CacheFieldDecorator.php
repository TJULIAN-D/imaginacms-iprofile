<?php

namespace Modules\Iprofile\Repositories\Cache;

use Modules\Iprofile\Repositories\FieldRepository;
use Modules\Core\Icrud\Repositories\Cache\BaseCacheCrudDecorator;

class CacheFieldDecorator extends BaseCacheCrudDecorator implements FieldRepository
{
    public function __construct(FieldRepository $field)
    {
        parent::__construct();
        $this->entityName = 'iprofile.fields';
        $this->tags = ['iprofile.userapis'];
        $this->repository = $field;
    }
}
