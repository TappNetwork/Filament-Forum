<?php

namespace Tapp\FilamentForum\Filament\Resources\Admin\ForumPostResource\Pages;

use Filament\Actions\EditAction;
use Tapp\FilamentForum\Filament\Resources\Admin\ForumPostResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewForumPost extends ViewRecord
{
    protected static string $resource = ForumPostResource::class;

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