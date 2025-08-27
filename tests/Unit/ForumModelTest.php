<?php

use Tapp\FilamentForum\Models\Forum;

test('forum has no guarded attributes', function () {
    $forum = new Forum;

    expect($forum->getGuarded())
        ->toBe([]);
});

test('forum has posts relationship method', function () {
    $forum = new Forum;

    expect(method_exists($forum, 'posts'))
        ->toBeTrue();
});

test('forum has forum posts relationship method', function () {
    $forum = new Forum;

    expect(method_exists($forum, 'forumPosts'))
        ->toBeTrue();
});
