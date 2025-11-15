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

    'tenancy' => [
        // Enable tenancy support
        'enabled' => false,

        // The Tenant model class (e.g., App\Models\Team::class, App\Models\Organization::class)
        'model' => null,

        // The tenant relationship name (defaults to snake_case of tenant model class name)
        // For example: Team::class -> 'team', Organization::class -> 'organization'
        // This should match what you configure in your Filament Panel:
        // ->tenantOwnershipRelationshipName('team')
        'relationship_name' => null,

        // The tenant column name (defaults to snake_case of tenant model class name + '_id')
        // You can override this if needed
        'column' => null,
    ],

    'forum' => [
        'slug' => 'forums',
    ],

    'forum-post' => [
        'slug' => 'forum-posts',
    ],

];
