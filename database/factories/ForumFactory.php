<?php

namespace Tapp\FilamentForum\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Tapp\FilamentForum\Models\Forum;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Tapp\FilamentForum\Models\Forum>
 */
class ForumFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Forum::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
        ];
    }
}
