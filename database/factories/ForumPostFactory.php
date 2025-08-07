<?php

namespace Tapp\FilamentForum\Database\Factories;

use Tapp\FilamentForum\Models\Forum;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Tapp\FilamentForum\Models\ForumPost>
 */
class ForumPostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $userModel = config('auth.providers.users.model');

        return [
            'name' => fake()->name(),
            'description' => fake()->sentence(),
            'user_id' => $userModel::factory(),
            'forum_id' => Forum::factory(),
        ];
    }
}
