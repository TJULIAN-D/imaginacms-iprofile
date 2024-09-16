<?php

namespace Modules\Iprofile\Entities;

use Modules\Core\Icrud\Entities\CrudModel;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use Modules\User\Entities\Sentinel\User;

class Department extends CrudModel
{
    use BelongsToTenant;

    protected $table = 'iprofile__departments';
    public $transformer = 'Modules\Iprofile\Transformers\DepartmentTransformer';
    public $repository = 'Modules\Iprofile\Repositories\DepartmentRepository';
    public $requestValidation = [
        'create' => 'Modules\Iprofile\Http\Requests\CreateDepartmentRequest',
        'update' => 'Modules\Iprofile\Http\Requests\UpdateDepartmentRequest',
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
        'title',
        'parent_id',
        'is_internal',
        'options'
    ];

    protected $fakeColumns = ['options'];

    protected $casts = [
        'options' => 'array'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'iprofile__user_department');
    }

    public function parent()
    {
        return $this->belongsTo(Department::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Department::class, 'parent_id');
    }

    public function settings()
    {
        return $this->hasMany(Setting::class, 'related_id')->where('entity_name', 'department');
    }

    public function getOptionsAttribute($value)
    {
        return json_decode($value);
    }

    public function setOptionsAttribute($value)
    {
        $this->attributes['options'] = json_encode($value);
    }
}