<?php

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Event;
use Tapp\FilamentForum\Events\ForumPostSubscribed;
use Tapp\FilamentForum\Events\ForumPostUnsubscribed;
use Tapp\FilamentForum\Events\ForumSubscribed;
use Tapp\FilamentForum\Events\ForumUnsubscribed;
use Tapp\FilamentForum\Models\Forum;
use Tapp\FilamentForum\Models\ForumPost;

beforeEach(function () {
    $this->userModel = config('filament-forum.user.model');
    $this->tenantModel = config('filament-forum.tenancy.model');
});

it('can toggle forum subscriptions with tenancy enabled', function () {
    Event::fake();

    $tenantModel = $this->tenantModel;
    $userModel = $this->userModel;

    $team = $tenantModel::factory()->create();
    $user = $userModel::factory()->create();

    actingAs($user);
    Filament::setTenant($team);

    $tenantColumn = Forum::getTenantColumnName();

    $forum = Forum::factory()->create([
        $tenantColumn => $team->id,
    ]);

    $forum->toggleSubscription();

    $user = $user->fresh();
    actingAs($user);

    expect($user->subscribedForums()->count())->toBe(1);
    expect($user->subscribedForums->first()->id)->toBe($forum->id);
    expect($forum->isSubscribed())->toBeTrue();
    expect($user->subscribedForums()->first()->pivot->{$tenantColumn})->toBe($team->id);

    Event::assertDispatched(ForumSubscribed::class, fn (ForumSubscribed $event): bool => $event->forum->is($forum) && $event->user->is($user));

    $forum->toggleSubscription();

    $user = $user->fresh();
    actingAs($user);

    expect($user->subscribedForums()->count())->toBe(0);
    expect($forum->isSubscribed())->toBeFalse();

    Event::assertDispatched(ForumUnsubscribed::class, fn (ForumUnsubscribed $event): bool => $event->forum->is($forum) && $event->user->is($user));
});

it('can toggle forum post subscriptions with tenancy enabled', function () {
    Event::fake();

    $tenantModel = $this->tenantModel;
    $userModel = $this->userModel;

    $team = $tenantModel::factory()->create();
    $user = $userModel::factory()->create();

    actingAs($user);
    Filament::setTenant($team);

    $tenantColumn = Forum::getTenantColumnName();

    $forum = Forum::factory()->create([
        $tenantColumn => $team->id,
    ]);

    $post = ForumPost::factory()->create([
        'forum_id' => $forum->id,
        'user_id' => $user->id,
        $tenantColumn => $team->id,
    ]);

    $post->toggleSubscription();

    $user = $user->fresh();
    actingAs($user);

    expect($user->subscribedForumPosts()->count())->toBe(1);
    expect($user->subscribedForumPosts->first()->id)->toBe($post->id);
    expect($post->isSubscribed())->toBeTrue();
    expect($user->subscribedForumPosts()->first()->pivot->{$tenantColumn})->toBe($team->id);

    Event::assertDispatched(ForumPostSubscribed::class, fn (ForumPostSubscribed $event): bool => $event->forumPost->is($post) && $event->user->is($user));

    $post->toggleSubscription();

    $user = $user->fresh();
    actingAs($user);

    expect($user->subscribedForumPosts()->count())->toBe(0);
    expect($post->isSubscribed())->toBeFalse();

    Event::assertDispatched(ForumPostUnsubscribed::class, fn (ForumPostUnsubscribed $event): bool => $event->forumPost->is($post) && $event->user->is($user));
});

it('scopes forum and post subscriptions to the current tenant', function () {
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
    $forum1->toggleSubscription();
    $post1->toggleSubscription();

    Filament::setTenant($team2);
    $forum2->toggleSubscription();
    $post2->toggleSubscription();

    expect($user->fresh()->subscribedForums()->count())->toBe(2);
    expect($user->fresh()->subscribedForumPosts()->count())->toBe(2);

    Filament::setTenant($team1);

    expect(Forum::subscribed()->sole()->id)->toBe($forum1->id);
    expect(ForumPost::subscribed()->sole()->id)->toBe($post1->id);

    Filament::setTenant($team2);

    expect(Forum::subscribed()->sole()->id)->toBe($forum2->id);
    expect(ForumPost::subscribed()->sole()->id)->toBe($post2->id);
});
