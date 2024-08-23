<?php

namespace Modules\Iprofile\Entities;

use Modules\Core\Icrud\Entities\CrudModel;
use Modules\User\Entities\Sentinel\User;

class Setting extends CrudModel
{

  protected $table = 'iprofile__settings';
  public $transformer = 'Modules\Iprofile\Transformers\SettingTransformer';
  public $repository = 'Modules\Iprofile\Repositories\SettingRepository';
  public $requestValidation = [
      'create' => 'Modules\Iprofile\Http\Requests\CreateSettingRequest',
      'update' => 'Modules\Iprofile\Http\Requests\UpdateSettingRequest',
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
  protected $fillable = [
    'related_id',
    'entity_name',
    'value',
    'name',
    'type'
  ];

  public function department()
  {
    $this->belognsTo(Department::class, 'related_id')->where('entity_name', 'user');
  }

  public function user()
  {
    $this->belognsTo(User::class, 'related_id')->where('entity_name', 'department');
  }

  public function getValueAttribute($value)
  {

    return json_decode($value);

  }

  public function setValueAttribute($value)
  {

    $this->attributes['value'] = json_encode($value);

  }
}
