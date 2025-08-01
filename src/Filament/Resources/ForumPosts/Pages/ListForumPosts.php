<?php

namespace Tapp\FilamentForum\Filament\Resources\ForumPosts\Pages;

use Filament\Resources\Pages\ListRecords;
use Tapp\FilamentForum\Filament\Resources\ForumPosts\ForumPostResource;

class ListForumPosts extends ListRecords
{
    protected static string $resource = ForumPostResource::class;
}
