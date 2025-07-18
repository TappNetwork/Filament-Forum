<?php

namespace Tapp\FilamentForum\Filament\Resources\Admin\ForumPostResource\Pages;

use Filament\Actions\CreateAction;
use Tapp\FilamentForum\Filament\Resources\Admin\ForumPostResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListForumPosts extends ListRecords
{
    protected static string $resource = ForumPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
} 