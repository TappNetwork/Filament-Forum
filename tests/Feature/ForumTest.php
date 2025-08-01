<?php

use Tapp\FilamentForum\Models\Forum;
use Tapp\FilamentForum\Models\ForumPost;

test('forum model exists', function () {
    expect(class_exists(Forum::class))
        ->toBeTrue();
});

test('forum post model exists', function () {
    expect(class_exists(ForumPost::class))
        ->toBeTrue();
});

test('forum has correct table name', function () {
    $forum = new Forum;

    expect($forum->getTable())
        ->toBe('forums');
});

test('forum post has correct table name', function () {
    $post = new ForumPost;

    expect($post->getTable())
        ->toBe('forum_posts');
});
