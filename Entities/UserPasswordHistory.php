<?php

namespace Modules\Iprofile\Entities;

use Modules\Core\Icrud\Entities\CrudModel;

class UserPasswordHistory extends CrudModel
{

    protected $table = 'iprofile__user_password_history';
    public $transformer = 'Modules\Iprofile\Transformers\UserPasswordHistoryTransformer';
    public $repository = 'Modules\Iprofile\Repositories\UserPasswordHistoryRepository';
    public $requestValidation = [
        'create' => 'Modules\Iprofile\Http\Requests\CreateUserPasswordHistoryRequest',
        'update' => 'Modules\Iprofile\Http\Requests\UpdateUserPasswordHistoryRequest',
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
        'user_id',
        'password'
    ];

    public function user()
    {
        $driver = config('asgard.user.config.driver');
        return $this->belongsTo("Modules\\User\\Entities\\{$driver}\\User");
    }

}