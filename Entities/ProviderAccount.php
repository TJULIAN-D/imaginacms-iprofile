<?php

namespace Modules\Iprofile\Entities;

use Modules\Core\Icrud\Entities\CrudModel;

class ProviderAccount extends CrudModel
{

  protected $table = 'iprofile__provider_accounts';
  public $transformer = 'Modules\Iprofile\Transformers\ProviderAccountTransformer';
  public $repository = 'Modules\Iprofile\Repositories\ProviderAccountRepository';
  public $requestValidation = [
      'create' => 'Modules\Iprofile\Http\Requests\CreateProviderAccountRequest',
      'update' => 'Modules\Iprofile\Http\Requests\UpdateProviderAccountRequest',
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
  protected $fillable = ['user_id', 'provider_user_id', 'provider' ,'options'];
  protected $casts = [
    'options'=>'array'
  ];

  protected $fakeColumns = ['options'];

    public function user()
    {
        $driver = config('asgard.user.config.driver');
        return $this->belongsTo("Modules\\User\\Entities\\{$driver}\\User");
    }

}
