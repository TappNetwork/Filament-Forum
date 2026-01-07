<?php

namespace Tapp\FilamentForum\Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Tapp\FilamentForum\Tests\Models\Team;

/**
 * @extends Factory<Team>
 */
class TeamFactory extends Factory
{
    protected $model = Team::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'slug' => fake()->unique()->slug(),
        ];
    }
}
