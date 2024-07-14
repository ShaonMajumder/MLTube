<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Video;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Comment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'body' => $this->faker->sentence(6),
            'user_id' => function(){
                //return factory(User::class)->create()->id; why it worked in Channel factory, not here
                //return factory('App\Models\User')->create()->id; not worked
                return \App\Models\User::factory()->create()->id;                
            },
            'video_id' => function(){
                //return factory(Video::class)->create()->id;
                return \App\Models\Video::factory()->create()->id;
            },
            'comment_id' => null
        ];
    }
}
