<?php

namespace Modules\Iprofile\Entities;

use Modules\Core\Icrud\Entities\CrudModel;
use Modules\User\Entities\Sentinel\User;
use Modules\Media\ValueObjects\MediaPath;

class Field extends CrudModel
{
    protected $table = 'iprofile__fields';
    public $transformer = 'Modules\Iprofile\Transformers\FieldTransformer';
    public $repository = 'Modules\Iprofile\Repositories\FieldRepository';
    public $requestValidation = [
        'create' => 'Modules\Iprofile\Http\Requests\CreateFieldRequest',
        'update' => 'Modules\Iprofile\Http\Requests\UpdateFieldRequest',
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
        'value',
        'name',
        'type'
    ];

    protected $fakeColumns = ['value'];

    protected $casts = [
        'value' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getValueAttribute($value)
    {
        if ($this->name == 'mainImage')
            return new MediaPath(json_decode($value));
        else return json_decode($value);
    }

    public function setValueAttribute($value)
    {
        if ($this->name == 'mainImage') {
            $url = $value;
            //Crear URL
            if (strpos($url, 'http') !== false){
                $url = str_replace(new MediaPath('/'), '', $value);
                $url = substr($url, 0, (strpos($url, "?") ?? strlen($url)));
            }
            //Change value
            $this->attributes['value'] = json_encode($url);
        } else $this->attributes['value'] = json_encode($value);
    }
}