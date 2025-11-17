<?php

use Tapp\FilamentForum\Models\Forum;
use Tapp\FilamentForum\Models\ForumPost;

beforeEach(function () {
    $this->userModel = config('filament-forum.user.model');
    $this->tenantModel = config('filament-forum.tenancy.model');
});

it('can create a forum', function () {
    $user = $this->userModel::factory()->create();
    $this->actingAs($user);

    $forum = Forum::factory()->create([
        'name' => 'Test Forum',
        'description' => 'A test forum',
    ]);

    expect($forum->name)->toBe('Test Forum');
    expect($forum->description)->toBe('A test forum');
});

it('can create a forum post', function () {
    $user = $this->userModel::factory()->create();
    $this->actingAs($user);

    $forum = Forum::factory()->create();

    $post = ForumPost::factory()->create([
        'forum_id' => $forum->id,
        'user_id' => $user->id,
        'title' => 'Test Post',
        'content' => 'Test content',
    ]);

    expect($post->title)->toBe('Test Post');
    expect($post->content)->toBe('Test content');
    expect($post->forum->id)->toBe($forum->id);
    expect($post->user->id)->toBe($user->id);
});

it('can toggle favorite on forum post', function () {
    $user = $this->userModel::factory()->create();
    $this->actingAs($user);

    $forum = Forum::factory()->create();
    $post = ForumPost::factory()->create([
        'forum_id' => $forum->id,
        'user_id' => $user->id,
    ]);

    // Toggle favorite
    $post->toggleFavorite();

    expect($user->fresh()->favoriteForumPosts()->count())->toBe(1);
    expect($post->isFavorite())->toBeTrue();

    // Toggle again to unfavorite
    $post->toggleFavorite();

    expect($user->fresh()->favoriteForumPosts()->count())->toBe(0);
    expect($post->isFavorite())->toBeFalse();
});
