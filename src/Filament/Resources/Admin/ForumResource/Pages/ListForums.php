<?php

namespace Tapp\FilamentForum\Filament\Resources\Admin\ForumResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;
use Tapp\FilamentForum\Filament\Resources\Admin\ForumResource;
use Tapp\FilamentForum\Filament\Resources\Admin\ForumResource\Widgets\ForumStats;

class ListForums extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = ForumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ForumStats::class,
        ];
    }
}
