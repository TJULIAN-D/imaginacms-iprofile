<?php

namespace Modules\Iprofile\Entities;

use Astrotomic\Translatable\Translatable;
use Modules\Core\Icrud\Entities\CrudModel;

use Modules\Media\Support\Traits\MediaRelation;

class Information extends CrudModel
{
  use Translatable, MediaRelation;

  protected $table = 'iprofile__information';
  public $transformer = 'Modules\Iprofile\Transformers\InformationTransformer';
  public $repository = 'Modules\Iprofile\Repositories\InformationRepository';
  public $requestValidation = [
      'create' => 'Modules\Iprofile\Http\Requests\CreateInformationRequest',
      'update' => 'Modules\Iprofile\Http\Requests\UpdateInformationRequest',
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
  public $translatedAttributes = ['title','description'];
  protected $fillable = ['user_id','type','options'];

  protected $casts = ['options' => 'array'];


  /**
   * Relations
   */
  public function user()
  {
    $driver = config('asgard.user.config.driver');
    return $this->belongsTo("Modules\\User\\Entities\\{$driver}\\User");
  }

  /**
   * Mutators
   */
  public function setOptionsAttribute($value)
  {
    $this->attributes['options'] = json_encode($value);
  }

  

}
