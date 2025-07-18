<?php

namespace Tapp\FilamentForum\Filament\Resources\ForumPosts\Pages;

use Tapp\FilamentForum\Filament\Resources\ForumPosts\ForumPostResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateForumPost extends CreateRecord
{
    protected static string $resource = ForumPostResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();

        return $data;
    }
} 