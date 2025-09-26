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

        // Custom search results closure for searchable selects
        // Example: fn (string $search): array => User::query()
        //     ->where('name', 'like', "%{$search}%")
        //     ->orWhere('email', 'like', "%{$search}%")
        //     ->limit(50)
        //     ->pluck('name', 'id')
        //     ->all()
        'search-results-using' => null,

        // Custom option label closure for searchable selects
        // Example: fn ($value): ?string => User::find($value)?->name
        'option-label-using' => null,

    ],
];
