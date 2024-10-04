<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PushNotificationAnalytics extends Model
{
    use HasFactory;
    protected $fillable = [
        'push_notification_id',
        'report_date',
        'total_received',
        'unique_users'
    ];
}
