<?php

namespace Tapp\FilamentForum\Filament\Resources\ForumResource\Resources\ForumPosts\Pages;

use Tapp\FilamentForum\Filament\Resources\ForumPostResource;
use Filament\Resources\Pages\CreateRecord;

class CreateForumPost extends CreateRecord
{
    protected static string $resource = ForumPostResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }
} 