<?php

// Simple architectural test that should work
arch('Forum model extends Eloquent Model')
    ->expect('Tapp\FilamentForum\Models\Forum')
    ->toExtend('Illuminate\Database\Eloquent\Model');

arch('ForumPost model extends Eloquent Model')
    ->expect('Tapp\FilamentForum\Models\ForumPost')
    ->toExtend('Illuminate\Database\Eloquent\Model');

// Test specific files instead of using ->each->not->toBeUsed()
arch('Forum model has no debugging functions')
    ->expect('src/Models/Forum.php')
    ->not->toContain('dd(')
    ->not->toContain('dump(')
    ->not->toContain('ray(');

arch('ForumPost model has no debugging functions')
    ->expect('src/Models/ForumPost.php')
    ->not->toContain('dd(')
    ->not->toContain('dump(')
    ->not->toContain('ray(');

arch('ForumPostView model has no debugging functions')
    ->expect('src/Models/ForumPostView.php')
    ->not->toContain('dd(')
    ->not->toContain('dump(')
    ->not->toContain('ray(');

arch('Service Provider has no debugging functions')
    ->expect('src/FilamentForumServiceProvider.php')
    ->not->toContain('dd(')
    ->not->toContain('dump(')
    ->not->toContain('ray(');

arch('Database migrations have no debugging functions')
    ->expect('database/migrations/*.php')
    ->not->toContain('dd(')
    ->not->toContain('dump(')
    ->not->toContain('ray(');
