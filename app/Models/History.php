<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;
    public $incrementing = false;

    protected static function boot(){
        
        parent::boot();
        static::creating(function($model){
            $model-> {$model->getKeyName()} = (string) Str::uuid();
        });
    }

}
