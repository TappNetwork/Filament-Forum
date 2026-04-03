<?php

namespace Tapp\FilamentForum\Filament\Resources\ForumPosts\Pages;

use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Tapp\FilamentForum\Concerns\HasCustomForumPostBreadcrumb;
use Tapp\FilamentForum\Filament\Resources\ForumPosts\ForumPostResource;
use Tapp\FilamentForum\Models\ForumPost;

class ViewForumPost extends ViewRecord
{
    use HasCustomForumPostBreadcrumb;

    protected static string $resource = ForumPostResource::class;

    protected function getHeaderActions(): array
    {
        return [];
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
}
