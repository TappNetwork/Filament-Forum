<?php

namespace Tapp\FilamentForum\Filament\Resources\ForumPosts\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use Tapp\FilamentForum\Filament\Resources\ForumPosts\ForumPostResource;
use Tapp\FilamentForum\Models\ForumPost;

class ViewForumPost extends ViewRecord
{
    protected static string $resource = ForumPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->visible(fn (): bool => Auth::check() && $this->getRecord()->user_id === Auth::id()),
        ];
    }

    public function getRecord(): ForumPost
    {
        /** @var ForumPost */
        return $this->record;
    }

    public function getTitle(): string|Htmlable
    {
        /** @phpstan-ignore-next-line */
        return $this->record->name;
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
