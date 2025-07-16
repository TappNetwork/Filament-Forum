<?php

namespace Tapp\FilamentForum\Filament\Resources\ForumResource\Resources\ForumPosts\Pages;

use Tapp\FilamentForum\Filament\Resources\ForumPostResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditForumPost extends EditRecord
{
    protected static string $resource = ForumPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
} 