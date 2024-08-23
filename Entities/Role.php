<?php

namespace Modules\Iprofile\Entities;

use Cartalyst\Sentinel\Roles\EloquentRole;
use Modules\Core\Icrud\Traits\hasEventsWithBindings;
use Modules\Iforms\Support\Traits\Formeable;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

use Astrotomic\Translatable\Translatable;

class Role extends EloquentRole
{
  use Formeable, BelongsToTenant, hasEventsWithBindings, Translatable;

  public function __construct(array $attributes = [])
  {

    parent::__construct($attributes);

  }

  public $transformer = 'Modules\Iprofile\Transformers\RoleTransformer';
  public $repository = 'Modules\Iprofile\Repositories\RoleRepository';
  public $requestValidation = [
      'create' => 'Modules\Iprofile\Http\Requests\CreateRoleRequest',
      'update' => 'Modules\Iprofile\Http\Requests\UpdateRoleRequest',
    ];
  //Instance external/internal events to dispatch with extraData
  public $dispatchesEventsWithBindings = [
    //eg. ['path' => 'path/module/event', 'extraData' => [/*...optional*/]]
    'created' => [],
    'creating' => [],
    'updated' => [],
    'updating' => [],
    'deleting' => [],
    'deleted' => []
  ];
  public $translatedAttributes = [
    'title'
  ];
  protected $fillable = [
    'slug',
    'name',
    'permissions'
  ];

  public $tenantWithCentralData = false;

  public function settings()
  {
    return $this->hasMany(Setting::class, 'related_id')->where('entity_name', 'role');
  }
}
