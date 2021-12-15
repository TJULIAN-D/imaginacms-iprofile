<?php

namespace Modules\Iprofile\Entities;

use Illuminate\Database\Eloquent\Model;
use Cartalyst\Sentinel\Roles\EloquentRole;
use Modules\Iforms\Support\Traits\Formeable;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Role extends EloquentRole
{
  use Formeable, BelongsToTenant;
  
  protected $fillable = [
    'slug',
    'name',
    'permissions'
  ];

  public function settings()
  {
    return $this->hasMany(Setting::class, 'related_id')->where('entity_name', 'role');
  }
}
