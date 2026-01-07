<?php

use Filament\Facades\Filament;
use Tapp\FilamentForum\Models\Forum;
use Tapp\FilamentForum\Models\ForumPost;

beforeEach(function () {
    // Skip these tests if tenancy is not enabled
    if (! config('filament-forum.tenancy.enabled')) {
        $this->markTestSkipped('Tenancy is not enabled');
    }

    $this->userModel = config('filament-forum.user.model');
    $this->tenantModel = config('filament-forum.tenancy.model');
});

it('can create a forum with tenant', function () {
    $tenant = $this->tenantModel::factory()->create();
    $user = $this->userModel::factory()->create();

    $this->actingAs($user);
    Filament::setTenant($tenant);

    $tenantColumn = Forum::getTenantRelationshipName().'_id';

    $forum = Forum::factory()->create([
        $tenantColumn => $tenant->id,
    ]);

    expect($forum->{$tenantColumn})->toBe($tenant->id);
});

it('scopes forums to current tenant', function () {
    $tenant1 = $this->tenantModel::factory()->create();
    $tenant2 = $this->tenantModel::factory()->create();
    $user = $this->userModel::factory()->create();

    $this->actingAs($user);

    $tenantColumn = Forum::getTenantRelationshipName().'_id';

    // Create forums for different tenants
    $forum1 = Forum::factory()->create([$tenantColumn => $tenant1->id]);
    $forum2 = Forum::factory()->create([$tenantColumn => $tenant2->id]);

    // Set tenant to tenant1 and verify scope
    Filament::setTenant($tenant1);
    $forums = Forum::all();

    expect($forums->count())->toBe(1);
    expect($forums->first()->id)->toBe($forum1->id);

    // Set tenant to tenant2 and verify scope
    Filament::setTenant($tenant2);
    $forums = Forum::all();

    expect($forums->count())->toBe(1);
    expect($forums->first()->id)->toBe($forum2->id);
});

it('scopes favorites to current tenant', function () {
    $tenant1 = $this->tenantModel::factory()->create();
    $tenant2 = $this->tenantModel::factory()->create();
    $user = $this->userModel::factory()->create();

    $this->actingAs($user);

    $tenantColumn = Forum::getTenantRelationshipName().'_id';

    $forum1 = Forum::factory()->create([$tenantColumn => $tenant1->id]);
    $forum2 = Forum::factory()->create([$tenantColumn => $tenant2->id]);

    $post1 = ForumPost::factory()->create([
        'forum_id' => $forum1->id,
        'user_id' => $user->id,
        $tenantColumn => $tenant1->id,
    ]);

    $post2 = ForumPost::factory()->create([
        'forum_id' => $forum2->id,
        'user_id' => $user->id,
        $tenantColumn => $tenant2->id,
    ]);

    // Favorite post1 in tenant1 context
    Filament::setTenant($tenant1);
    $post1->toggleFavorite();

    // Favorite post2 in tenant2 context
    Filament::setTenant($tenant2);
    $post2->toggleFavorite();

    // Verify both favorites exist in database
    expect($user->favoriteForumPosts()->count())->toBe(2);

    // Set tenant to tenant1 and check scope
    Filament::setTenant($tenant1);
    $favoritedInTenant1 = ForumPost::favorited()->get();
    expect($favoritedInTenant1->count())->toBe(1);
    expect($favoritedInTenant1->first()->id)->toBe($post1->id);

    // Set tenant to tenant2 and check scope
    Filament::setTenant($tenant2);
    $favoritedInTenant2 = ForumPost::favorited()->get();
    expect($favoritedInTenant2->count())->toBe(1);
    expect($favoritedInTenant2->first()->id)->toBe($post2->id);
});
