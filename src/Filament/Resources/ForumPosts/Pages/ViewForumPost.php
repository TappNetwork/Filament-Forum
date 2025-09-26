<?php

namespace Tapp\FilamentForum\Filament\Resources\ForumPosts\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use Tapp\FilamentForum\Concerns\HasCustomForumPostBreadcrumb;
use Tapp\FilamentForum\Filament\Resources\ForumPosts\ForumPostResource;
use Tapp\FilamentForum\Models\ForumPost;

class ViewForumPost extends ViewRecord
{
    use HasCustomForumPostBreadcrumb;

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
}
