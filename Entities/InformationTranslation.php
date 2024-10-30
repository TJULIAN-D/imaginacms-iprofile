<?php

namespace Modules\Iprofile\Entities;

use Illuminate\Database\Eloquent\Model;

class InformationTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['title','description'];
    protected $table = 'iprofile__information_translations';
}
