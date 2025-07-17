<?php

namespace Tapp\FilamentForum\Filament\Resources\ForumPosts\Pages;

use Tapp\FilamentForum\Filament\Resources\ForumPosts\ForumPostResource;
use Filament\Resources\Pages\CreateRecord;

class CreateForumPost extends CreateRecord
{
    protected static string $resource = ForumPostResource::class;
} 