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

];
