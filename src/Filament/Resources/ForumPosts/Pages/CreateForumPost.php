<?php

namespace Tapp\FilamentForum\Filament\Resources\ForumPosts\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Tapp\FilamentForum\Concerns\HasCustomForumPostBreadcrumb;
use Tapp\FilamentForum\Filament\Resources\ForumPosts\ForumPostResource;

class CreateForumPost extends CreateRecord
{
    use HasCustomForumPostBreadcrumb;

    protected static string $resource = ForumPostResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();

        return $data;
    }
}
