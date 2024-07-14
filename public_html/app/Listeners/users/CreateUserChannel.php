<?php

namespace App\Listeners\users;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateUserChannel
{
  
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        //dd($event->user->name);
        $event->user->channel()->create([
            'name' => $event->user->name
        ]);
    }
}
