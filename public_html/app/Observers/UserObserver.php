<?php

namespace App\Observers;

use App\Enums\RoleEnum;
use App\Enums\UserEnum;
use App\Models\Settings;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    /**
     * Handle the User "created" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function created(User $user)
    {
        if($user->account_type == RoleEnum::ADMIN){
            if(!$user->hasRole(RoleEnum::ADMIN)){
                $user->attachRole(RoleEnum::ADMIN);
            }
        }

        if(!$user->hasRole(RoleEnum::VIEWER)){
            $user->attachRole(RoleEnum::VIEWER);
        }
    }

    /**
     * Handle the User "updated" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function updated(User $user)
    {
        $user_id = $user->id;
        if ($user->wasChanged('status')) {
            Log::info("Observer of User :: user=$user_id, Status was changed to $user->status");
            // If User Status is changed to inactive or suspended, user can not automatically access roles, so it not needed to change roles.
        }

        if ($user->wasChanged('account_type')) {
            Log::info("Observer of User :: user=$user_id, Account Type was changed to $user->account_type");
            if ($user->account_type === RoleEnum::ADMIN) {
                if (!$user->hasRole(RoleEnum::ADMIN)) {
                    $user->attachRole(RoleEnum::ADMIN);
                }
            }
        }
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function deleted(User $user)
    {
        if($user->hasRole(RoleEnum::VIEWER)){
            $user->detachRole(RoleEnum::VIEWER);
        }

        Settings::where('user_id', $user->id)->delete();
    }

    /**
     * Handle the User "restored" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function restored(User $user)
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function forceDeleted(User $user)
    {
        //
    }
}
