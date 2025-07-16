<?php

namespace Tapp\FilamentForum\Filament\Resources\ForumPosts\Pages;

use Filament\Resources\Pages\ListRecords;
use Tapp\FilamentForum\Filament\Resources\ForumPostsResource;

class ListForumPosts extends ListRecords
{
    protected static string $resource = ForumPostsResource::class;
} 