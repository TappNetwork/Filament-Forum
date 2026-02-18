<?php

namespace Tapp\FilamentForum\Filament\Resources\Admin\ForumPostResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;
use Tapp\FilamentForum\Filament\Resources\Admin\ForumPostResource;
use Tapp\FilamentForum\Filament\Resources\Admin\ForumPostResource\Widgets\ForumPostStats;

class ListForumPosts extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = ForumPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ForumPostStats::class,
        ];
    }
}
