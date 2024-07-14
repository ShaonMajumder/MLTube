<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as BaseModel;

class Model extends BaseModel
{
    use HasFactory;
    public $incrementing = false;
    protected $guarded =[];

    protected static function boot(){
        
        parent::boot();
        static::creating(function($model){
            $model-> {$model->getKeyName()} = (string) Str::uuid();
        });
    }
}
