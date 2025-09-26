<?php

namespace Tapp\FilamentForum\Filament\Resources\ForumPosts\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Kirschbaum\Commentions\Filament\Actions\CommentsAction;
use Tapp\FilamentForum\Filament\Resources\ForumPosts\ForumPostResource;
use Tapp\FilamentForum\Models\ForumPost;

class EditForumPost extends EditRecord
{
    protected static string $resource = ForumPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CommentsAction::make(),
            DeleteAction::make()
                ->visible(fn (): bool => Auth::check() && $this->getRecord()->user_id === Auth::id()),
        ];
    }

    public function getRecord(): ForumPost
    {
        /** @var ForumPost */
        return $this->record;
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
