<?php

use Filament\Facades\Filament;
use Tapp\FilamentForum\Models\Forum;
use Tapp\FilamentForum\Models\ForumPost;
use Workbench\App\Models\Team;
use Workbench\App\Models\User;

beforeEach(function () {
    // Enable tenancy for tests
    config(['filament-forum.tenancy.enabled' => true]);
    config(['filament-forum.tenancy.model' => Team::class]);
});

it('can toggle favorite with tenancy enabled', function () {
    $team = Team::factory()->create();
    $user = User::factory()->create();

    // Set current tenant
    Filament::setTenant($team);
    actingAs($user);

    $forum = Forum::factory()->create([
        'team_id' => $team->id,
    ]);

    $post = ForumPost::factory()->create([
        'forum_id' => $forum->id,
        'user_id' => $user->id,
        'team_id' => $team->id,
    ]);

    // Toggle favorite
    $post->toggleFavorite();

    // Check that the favorite was created with team_id
    expect($user->favoriteForumPosts()->count())->toBe(1);
    expect($user->favoriteForumPosts->first()->id)->toBe($post->id);

    // Verify pivot data includes team_id
    $pivot = $user->favoriteForumPosts()->first()->pivot;
    expect($pivot->team_id)->toBe($team->id);

    // Check isFavorite returns true
    expect($post->isFavorite())->toBeTrue();

    // Toggle again to unfavorite
    $post->toggleFavorite();
    expect($user->favoriteForumPosts()->count())->toBe(0);
    expect($post->isFavorite())->toBeFalse();
});

it('scopes favorites to current tenant', function () {
    $team1 = Team::factory()->create();
    $team2 = Team::factory()->create();
    $user = User::factory()->create();

    actingAs($user);

    $forum1 = Forum::factory()->create(['team_id' => $team1->id]);
    $forum2 = Forum::factory()->create(['team_id' => $team2->id]);

    $post1 = ForumPost::factory()->create([
        'forum_id' => $forum1->id,
        'user_id' => $user->id,
        'team_id' => $team1->id,
    ]);

    $post2 = ForumPost::factory()->create([
        'forum_id' => $forum2->id,
        'user_id' => $user->id,
        'team_id' => $team2->id,
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
    // Disable tenancy
    config(['filament-forum.tenancy.enabled' => false]);

    $user = User::factory()->create();
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
