<?php

namespace Tapp\FilamentForum\Filament\Resources\ForumResource\Resources\ForumPosts\Pages;

use Tapp\FilamentForum\Filament\Resources\ForumPostResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewForumPost extends ViewRecord
{
    protected static string $resource = ForumPostResource::class;

    public function mount($record): void
    {
        parent::mount($record);

        // Record the view when the page is loaded
        /** @phpstan-ignore-next-line */
        $this->record->recordView();
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    public function getTitle(): string | Htmlable
    {
        /** @phpstan-ignore-next-line */
        return $this->record->name;
    }
} 