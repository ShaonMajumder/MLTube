<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laratrust\Traits\LaratrustUserTrait;
use Laratrust\Contracts\LaratrustUserInterface;
use Laratrust\Traits\LaratrustPermissionTrait;
use Laratrust\Traits\LaratrustRoleTrait;


class User extends Authenticatable implements MustVerifyEmail
// LaratrustUserInterface
{
    use LaratrustUserTrait;
    // use LaratrustRoleTrait;
    // use LaratrustPermissionTrait;
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    public $incrementing = false;

    protected static function boot(){
        
        parent::boot();
        static::creating(function($model){
            $model-> {$model->getKeyName()} = (string) Str::uuid();
        });
    }

    public function channel(){
        return $this->hasOne(Channel::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function comments(){
        return $this->hasMany(Comment::class);
    }

    public function toggleVote($entity, $type){
        $vote = $entity->votes->where('user_id', $this->id)->first();

        if($vote){
            $vote->update([
                'type' => $type
            ]);
            return $vote->refresh();
        }else{
            return $entity->votes()->create([
                'type' => $type,
                'user_id' => $this->id
            ]);
        }
    }

    public function subscriptions(): HasManyThrough
    {
        // From User Model File, catch Subscription Model by user_id, catch Channel Model by channel_id, use hasmanythrough
        return $this->hasManyThrough(
            Channel::class,      // The final model we want to access (Channel)
            Subscription::class, // The intermediate model (Subscription)
            'user_id',           // Foreign key on the Subscription model that refers to the User
            'id',                // Foreign key on the Channel model (refers to Channel itself, should match primary key)
            'id',                // Local key on the User model
            'channel_id'         // Local key on the Subscription model (refers to Channel)
        );
    }
}
