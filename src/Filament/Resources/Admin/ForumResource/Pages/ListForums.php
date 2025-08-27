<?php

namespace Tapp\FilamentForum\Filament\Resources\Admin\ForumResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Tapp\FilamentForum\Filament\Resources\Admin\ForumResource;

class ListForums extends ListRecords
{
    protected static string $resource = ForumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
