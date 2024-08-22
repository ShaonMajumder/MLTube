<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;  //
//use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    public function subscriberChannel(){
        return $this->hasOne(Channel::class, 'channel_id');
    }

    /**
     * Get the user associated with the subscription.
     */
    public function subscriber()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the channel associated with the subscription.
     */
    // public function channel()
    // {
    //     // First get the user by user_id, then get the channel by the user's ID
    //     return $this->subscriber()->hasOne(Channel::class, 'user_id');
    // }

    /**
     * Get the channel associated with the subscription through the user.
     */
    public function channel()
    {
        // return $this->hasOneThrough(Channel::class, User::class, 'id', 'user_id');
        return $this->hasOneThrough(
            Channel::class, // The model you want to access
            User::class,    // The intermediate model
            'id',           // Foreign key on the intermediate model (User's ID)
            'user_id',      // Foreign key on the target model (Channel's user_id)
            'user_id',      // Local key on the Subscription model (Subscription's user_id)
            'id'            // Local key on the intermediate model (User's ID)
        );
    }
}
