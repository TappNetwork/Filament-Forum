<?php

namespace Tapp\FilamentForum\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Tapp\FilamentForum\Models\Forum;
use Tapp\FilamentForum\Models\ForumPost;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Tapp\FilamentForum\Models\ForumPost>
 */
class ForumPostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Tapp\FilamentForum\Models\ForumPost>
     */
    protected $model = ForumPost::class;

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
