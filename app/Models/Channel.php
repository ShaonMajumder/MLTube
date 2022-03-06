<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\Models\Media;

class Channel extends Model implements HasMedia
{
    use HasFactory;
    protected $table = 'channels'; 
    use HasMediaTrait;
    
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function image(){
        if($this->media->first()){
            return $this->media->first()->getFullUrl('thumb');//url is not correct
        }
        return null;
    }

    public function editable(){
    	if( ! auth()->check() ) return false;
    	return $this->user_id === auth()->user()->id;
    }

    public function registerMediaConversions(?Media $media = null){ //unmatch
        $this->addMediaConversion('thumb')->width(100)->height(100);
    }

    public function subscriptions(){
        return $this->hasMany(Subscription::class);
    }

    public function videos(){
        return $this->hasMany(Video::class);
    }
}
