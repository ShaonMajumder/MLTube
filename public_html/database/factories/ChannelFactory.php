<?php

namespace Database\Factories;

use App\Models\Channel;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChannelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Channel::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "name" => $this->faker->sentence(3),
            "user_id" => function(){
                //return factory(User::class)->create()->id; dont work
                return User::factory()->create()->id;
            },
            "description" => $this->faker->sentence(30)
        ];
    }
}
