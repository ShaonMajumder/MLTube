<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePushNotificationAnalyticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('push_notification_analytics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('push_notification_id');
            $table->unsignedBigInteger('total_viewed')->default(0.0);
            $table->unsignedBigInteger('total_users')->default(0.0);
            $table->date('report_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('push_notification_analytics');
    }
}
