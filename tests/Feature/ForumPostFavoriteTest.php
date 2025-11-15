<?php

use Filament\Facades\Filament;
use Tapp\FilamentForum\Models\Forum;
use Tapp\FilamentForum\Models\ForumPost;

beforeEach(function () {
    // Get models from config
    $this->userModel = config('filament-forum.user.model', 'App\\Models\\User');
    $this->tenantModel = config('filament-forum.tenancy.model', 'App\\Models\\Team');

    // Enable tenancy for tests
    config(['filament-forum.tenancy.enabled' => true]);
    config(['filament-forum.tenancy.model' => $this->tenantModel]);
});

it('can toggle favorite with tenancy enabled', function () {
    $tenantModel = $this->tenantModel;
    $userModel = $this->userModel;

    $team = $tenantModel::factory()->create();
    $user = $userModel::factory()->create();

    // Set current tenant
    Filament::setTenant($team);
    actingAs($user);

    // Get the tenant column name dynamically
    $tenantColumn = Forum::getTenantColumnName();

    $forum = Forum::factory()->create([
        $tenantColumn => $team->id,
    ]);

    $post = ForumPost::factory()->create([
        'forum_id' => $forum->id,
        'user_id' => $user->id,
        $tenantColumn => $team->id,
    ]);

    // Toggle favorite
    $post->toggleFavorite();

    // Check that the favorite was created with tenant_id
    expect($user->favoriteForumPosts()->count())->toBe(1);
    expect($user->favoriteForumPosts->first()->id)->toBe($post->id);

    // Verify pivot data includes tenant_id
    $pivot = $user->favoriteForumPosts()->first()->pivot;
    expect($pivot->{$tenantColumn})->toBe($team->id);

    // Check isFavorite returns true
    expect($post->isFavorite())->toBeTrue();

    // Toggle again to unfavorite
    $post->toggleFavorite();
    expect($user->favoriteForumPosts()->count())->toBe(0);
    expect($post->isFavorite())->toBeFalse();
});

it('scopes favorites to current tenant', function () {
    $tenantModel = $this->tenantModel;
    $userModel = $this->userModel;

    $team1 = $tenantModel::factory()->create();
    $team2 = $tenantModel::factory()->create();
    $user = $userModel::factory()->create();

    actingAs($user);

    // Get the tenant column name dynamically
    $tenantColumn = Forum::getTenantColumnName();

    $forum1 = Forum::factory()->create([$tenantColumn => $team1->id]);
    $forum2 = Forum::factory()->create([$tenantColumn => $team2->id]);

    $post1 = ForumPost::factory()->create([
        'forum_id' => $forum1->id,
        'user_id' => $user->id,
        $tenantColumn => $team1->id,
    ]);

    $post2 = ForumPost::factory()->create([
        'forum_id' => $forum2->id,
        'user_id' => $user->id,
        $tenantColumn => $team2->id,
    ]);

    // Favorite post1 in team1 context
    Filament::setTenant($team1);
    $post1->toggleFavorite();

    // Favorite post2 in team2 context
    Filament::setTenant($team2);
    $post2->toggleFavorite();

    // Verify both favorites exist in database
    expect($user->favoriteForumPosts()->count())->toBe(2);

    // Set tenant to team1 and check scope
    Filament::setTenant($team1);
    $favoritedInTeam1 = ForumPost::favorited()->get();
    expect($favoritedInTeam1->count())->toBe(1);
    expect($favoritedInTeam1->first()->id)->toBe($post1->id);

    // Set tenant to team2 and check scope
    Filament::setTenant($team2);
    $favoritedInTeam2 = ForumPost::favorited()->get();
    expect($favoritedInTeam2->count())->toBe(1);
    expect($favoritedInTeam2->first()->id)->toBe($post2->id);
});

it('works without tenancy when disabled', function () {
    $userModel = $this->userModel;

    // Disable tenancy
    config(['filament-forum.tenancy.enabled' => false]);

    $user = $userModel::factory()->create();
    actingAs($user);

    $forum = Forum::factory()->create();
    $post = ForumPost::factory()->create([
        'forum_id' => $forum->id,
        'user_id' => $user->id,
    ]);

    // Toggle favorite
    $post->toggleFavorite();

    // Check that the favorite was created
    expect($user->favoriteForumPosts()->count())->toBe(1);
    expect($post->isFavorite())->toBeTrue();

    // Toggle again to unfavorite
    $post->toggleFavorite();
    expect($user->favoriteForumPosts()->count())->toBe(0);
    expect($post->isFavorite())->toBeFalse();
});
