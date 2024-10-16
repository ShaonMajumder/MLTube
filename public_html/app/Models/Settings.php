<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Settings extends Model
{
    use HasFactory,SoftDeletes;

    public function togglePushNotifications(){
        $pushNotificationSettings = Settings::where('key', 'push_notifications')->first();
        if(!$pushNotificationSettings){
            Settings::create(['key'=>'push_notifications','value'=>true]);
        }
        $pushNotificationSettings->update(['value'=> !$pushNotificationSettings->value]);
    }
}
