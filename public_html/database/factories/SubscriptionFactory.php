<?php

namespace Database\Factories;

use App\Models\Channel;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Subscription::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "user_id" => function(){
                //return factory(User::class)->create()->id; why it worked in Channel factory, not here
                //return factory('App\Models\User')->create()->id; not worked
                return User::factory()->create()->id;                
            },
            "channel_id" => function(){
                //return factory(Channel::class)->create()->id;
                return Channel::factory()->create()->id;
            }
        ];
    }
}
