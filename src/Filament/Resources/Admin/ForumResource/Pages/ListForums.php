<?php

namespace Tapp\FilamentForum\Filament\Resources\Admin\ForumResource\Pages;

use Filament\Actions\CreateAction;
use Tapp\FilamentForum\Filament\Resources\Admin\ForumResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

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