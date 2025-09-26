<?php

return [

    'resources' => [
        'forumResource' => \Tapp\FilamentForum\Filament\Resources\Forums\ForumResource::class,
        'forumPostResource' => \Tapp\FilamentForum\Filament\Resources\ForumPosts\ForumPostResource::class,
    ],

    'admin-resources' => [
        'adminForumResource' => \Tapp\FilamentForum\Filament\Resources\Admin\ForumResource::class,
        'adminForumPostResource' => \Tapp\FilamentForum\Filament\Resources\Admin\ForumPostResource::class,
    ],

    'user' => [
        // The title attribute of user relationship. Can be an accessor in your User model
        'title-attribute' => 'name',

        // The User model class (used for custom search functionality)
        'model' => 'App\\Models\\User',
    ],

    'frontend' => [
        'forum-posts' => [
            'title' => 'Forum Posts',
            'breadcrumb' => 'Forum Posts',
        ],
    ],

];
