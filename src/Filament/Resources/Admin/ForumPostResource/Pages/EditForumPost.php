<?php

namespace Tapp\FilamentForum\Filament\Resources\Admin\ForumPostResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Tapp\FilamentForum\Filament\Resources\Admin\ForumPostResource;

class EditForumPost extends EditRecord
{
    protected static string $resource = ForumPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
