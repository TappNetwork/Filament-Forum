<?php

namespace Tapp\FilamentForum\Filament\Resources\Admin\ForumResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Tapp\FilamentForum\Filament\Resources\Admin\ForumResource;

class EditForum extends EditRecord
{
    protected static string $resource = ForumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
