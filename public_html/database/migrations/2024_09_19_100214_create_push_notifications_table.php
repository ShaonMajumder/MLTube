<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePushNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('push_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('message')->nullable();
            $table->boolean('status')->default(true);
            $table->string('thumbnail_desktop')->nullable();
            $table->string('thumbnail_mobile')->nullable();
            $table->string('url');
            $table->timestamp('activate_at')->nullable(); //->default(now());
            $table->timestamp('inactivate_at')->nullable();
            $table->time('schedule_time')->nullable();
            $table->unsignedBigInteger('total_sent')->default(0);
            $table->unsignedBigInteger('total_received')->default(0);
            $table->unsignedBigInteger('total_viewed')->default(0);
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
        Schema::dropIfExists('push_notifications');
    }
}
