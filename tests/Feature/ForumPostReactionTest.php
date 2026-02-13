<?php

use Filament\Facades\Filament;
use Tapp\FilamentForum\Models\Forum;
use Tapp\FilamentForum\Models\ForumPost;
use Tapp\FilamentForum\Models\ForumPostReaction;

beforeEach(function () {
    $this->userModel = config('filament-forum.user.model');
    $this->tenantModel = config('filament-forum.tenancy.model');
});

it('can toggle reaction on forum post', function () {
    $tenantModel = $this->tenantModel;
    $userModel = $this->userModel;

    $team = $tenantModel::factory()->create();
    $user = $userModel::factory()->create();

    actingAs($user);
    Filament::setTenant($team);

    $tenantColumn = Forum::getTenantColumnName();
    $forum = Forum::factory()->create([$tenantColumn => $team->id]);
    $post = ForumPost::factory()->create([
        'forum_id' => $forum->id,
        'user_id' => $user->id,
        $tenantColumn => $team->id,
    ]);

    $post->toggleReaction('👍', $user);

    expect($post->getReactionCounts())->toBe(['👍' => 1]);
    expect($post->getUserReactions($user)->toArray())->toBe(['👍']);
    expect(ForumPostReaction::where('forum_post_id', $post->id)->count())->toBe(1);

    $post->toggleReaction('👍', $user);

    expect($post->getReactionCounts())->toBe([]);
    expect($post->getUserReactions($user)->toArray())->toBe([]);
    expect(ForumPostReaction::where('forum_post_id', $post->id)->count())->toBe(0);
});

it('rejects invalid reaction types', function () {
    $tenantModel = $this->tenantModel;
    $userModel = $this->userModel;

    $team = $tenantModel::factory()->create();
    $user = $userModel::factory()->create();

    actingAs($user);
    Filament::setTenant($team);

    $tenantColumn = Forum::getTenantColumnName();
    $forum = Forum::factory()->create([$tenantColumn => $team->id]);
    $post = ForumPost::factory()->create([
        'forum_id' => $forum->id,
        'user_id' => $user->id,
        $tenantColumn => $team->id,
    ]);

    $post->toggleReaction('invalid-reaction-type', $user);

    expect(ForumPostReaction::where('forum_post_id', $post->id)->count())->toBe(0);
});

it('scopes reactions to current tenant', function () {
    $tenantModel = $this->tenantModel;
    $userModel = $this->userModel;

    $team1 = $tenantModel::factory()->create();
    $team2 = $tenantModel::factory()->create();
    $user = $userModel::factory()->create();

    actingAs($user);

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

    Filament::setTenant($team1);
    $post1->toggleReaction('❤️', $user);

    Filament::setTenant($team2);
    $post2->toggleReaction('😂', $user);

    expect(ForumPostReaction::count())->toBe(2);

    $reaction1 = ForumPostReaction::where('forum_post_id', $post1->id)->first();
    $reaction2 = ForumPostReaction::where('forum_post_id', $post2->id)->first();

    expect($reaction1->{$tenantColumn})->toBe($team1->id);
    expect($reaction2->{$tenantColumn})->toBe($team2->id);
});
