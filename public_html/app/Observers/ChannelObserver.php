<?php

namespace App\Observers;

use App\Enums\ChannelEnum;
use App\Enums\RoleEnum;
use App\Models\Channel;

class ChannelObserver
{
    /**
     * Handle the Channel "created" event.
     *
     * @param  \App\Models\Channel  $channel
     * @return void
     */
    public function created(Channel $channel)
    {
        $user = $channel->user;
        if(!$user->hasRole(RoleEnum::CHANNEL_OWNER)){
            $user->attachRole(RoleEnum::CHANNEL_OWNER);
        }
    }

    /**
     * Handle the Channel "updated" event.
     *
     * @param  \App\Models\Channel  $channel
     * @return void
     */
    public function updated(Channel $channel)
    {
        if ($channel->wasChanged('status')) {
            $user = $channel->user;
            if ($channel->status === ChannelEnum::STATUS['INACTIVE'] ) {
                if($user->hasRole(RoleEnum::CHANNEL_OWNER)){
                    $user->detachRole(RoleEnum::CHANNEL_OWNER);
                }
            } elseif ($channel->status === ChannelEnum::STATUS['ACTIVE'] ) {
                if(!$user->hasRole(RoleEnum::CHANNEL_OWNER)){
                    $user->attachRole(RoleEnum::CHANNEL_OWNER);
                }
            }
        }
    }

    /**
     * Handle the Channel "deleted" event.
     *
     * @param  \App\Models\Channel  $channel
     * @return void
     */
    public function deleted(Channel $channel)
    {
        $user = $channel->user;
        if($user->hasRole(RoleEnum::CHANNEL_OWNER)){
            $user->detachRole(RoleEnum::CHANNEL_OWNER);
        }
    }

    /**
     * Handle the Channel "restored" event.
     *
     * @param  \App\Models\Channel  $channel
     * @return void
     */
    public function restored(Channel $channel)
    {
        //
    }

    /**
     * Handle the Channel "force deleted" event.
     *
     * @param  \App\Models\Channel  $channel
     * @return void
     */
    public function forceDeleted(Channel $channel)
    {
        //
    }
}
