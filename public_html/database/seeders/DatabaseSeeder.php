<?php

namespace Database\Seeders;

use App\Models\Channel;
use App\Models\Comment;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Video;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user1 = User::factory()->create([
            "name" => "Global Admin",
            "email"=> "admin@admin.com",
            "password" => bcrypt("123456"),
            "account_type" => "admin",
            "status" => 1
        ]);

        $user2 = User::factory()->create([
            "name" => "Shaon Majumder",
            "email"=> "smazoomder@gmail.com",
            "password" => bcrypt("123456"),
            "status" => 1
        ]);

        $user3 = User::factory()->create([
            "name" => "Jan Doe",
            "email"=> "jandoe@gmail.com",
            "password" => bcrypt("123456"),
            "status" => 1
        ]);
        
        //creating channel
        //$channel1 = factory(Channel::class)->create([ "user_id"=> $channel1->id ]);
        //$channel2 = factory(Channel::class)->create([ "user_id"=> $channel2->id ]);
        $channel1 = Channel::factory()->create([
            "user_id"=> $user1->id,
            "name" => $user1->name
        ]);
        $channel2 = Channel::factory()->create([
            "user_id"=> $user2->id,
            "name" => $user2->name
        ]);

        $channel3 = Channel::factory()->create([
            "user_id"=> $user3->id,
            "name" => $user3->name
        ]);

        //subscribe to each others channel
        $channel1->subscriptions()->create([ "user_id" => $channel2->user_id ]);
        $channel1->subscriptions()->create([ "user_id" => $channel3->user_id ]);
        $channel2->subscriptions()->create([ "user_id" => $channel1->user_id ]);
        $channel3->subscriptions()->create([ "user_id" => $channel1->user_id ]);

        //creating subscriptions bulkly for both user, for testing in mass scale, to run efficiently.
        Subscription::factory(10)->create([ "channel_id" => $channel1->id ]);
        Subscription::factory(10)->create([ "channel_id" => $channel2->id ]);
        Subscription::factory(10)->create([ "channel_id" => $channel3->id ]);
        
        //factory(Subscription::class, 10000)->create([ "channel_id" => $channel1->id ]); don't work
        //factory(Subscription::class, 10000)->create([ "channel_id" => $channel2->id ]); don't work

        $video = Video::factory()->create([
            'channel_id' => $channel1->id
        ]);

        Comment::factory(50)->create([
            'video_id' => $video->id
        ]);

        $comment = Comment::first();

        Comment::factory(50)->create([
            'video_id' => $video->id,
            'comment_id' => $comment->id
        ]);
    }
}