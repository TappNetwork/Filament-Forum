<?php

namespace Tapp\FilamentForum\Filament\Resources\Admin\ForumPostResource\Pages;

use Filament\Actions\DeleteAction;
use Tapp\FilamentForum\Filament\Resources\Admin\ForumPostResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Kirschbaum\Commentions\Filament\Actions\CommentsAction;

class EditForumPost extends EditRecord
{
    protected static string $resource = ForumPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CommentsAction::make(),
            DeleteAction::make(),
        ];
    }
} 