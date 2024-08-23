<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\Models\Media;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

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

    public function videos(){
        return $this->hasMany(Video::class);
    }

    public function subscribers(): HasManyThrough
    {
        //  From Chanel, catch Subscription model by channel_id , then catch User model by Subscription->user_id use has many through
        return $this->hasManyThrough(
            User::class,          // Final model we want to access
            Subscription::class,  // Intermediate model
            'channel_id',         // Foreign key on the Subscription model (refers to Channel)
            'id',                 // Foreign key on the User model (refers to User)
            'id',                 // Local key on the Channel model
            'user_id'             // Local key on the Subscription model (refers to User)
        );
    }

    // confuwsinh name
    public function subscriptions(){
        return $this->hasMany(Subscription::class);
    }
}
