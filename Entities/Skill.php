<?php

namespace Modules\Iprofile\Entities;

use Modules\Core\Icrud\Entities\CrudModel;

class Skill extends CrudModel
{
  
  protected $table = 'iprofile__skills';
  public $transformer = 'Modules\Iprofile\Transformers\SkillTransformer';
  public $repository = 'Modules\Iprofile\Repositories\SkillRepository';
  public $requestValidation = [
      'create' => 'Modules\Iprofile\Http\Requests\CreateSkillRequest',
      'update' => 'Modules\Iprofile\Http\Requests\UpdateSkillRequest',
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

  protected $fillable = ['title','user_id','entity_type','entity_id'];

  /**
   * Relations
   */
  public function user()
  {
    $driver = config('asgard.user.config.driver');
    return $this->belongsTo("Modules\\User\\Entities\\{$driver}\\User");
  }
  
}
