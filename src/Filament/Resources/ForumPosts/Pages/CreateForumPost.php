<?php

namespace Tapp\FilamentForum\Filament\Resources\ForumPosts\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Tapp\FilamentForum\Filament\Resources\ForumPosts\ForumPostResource;

class CreateForumPost extends CreateRecord
{
    protected static string $resource = ForumPostResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();

        return $data;
    }

    public function getBreadcrumbs(): array
    {
        $forumResource = config('filament-forum.resources.forumResource');
        $forumRecord = $this->getParentRecord();

        return [
            $forumResource::getUrl('index') => config('filament-forum.frontend.forum.breadcrumb'),
            $forumResource::getUrl('forum-posts', ['record' => $forumRecord]) => config('filament-forum.frontend.forum-posts.breadcrumb'),
            '' => 'View',
        ];
    }
}
