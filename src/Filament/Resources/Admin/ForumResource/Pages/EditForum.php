<?php

namespace Tapp\FilamentForum\Filament\Resources\Admin\ForumResource\Pages;

use Filament\Actions\DeleteAction;
use Tapp\FilamentForum\Filament\Resources\Admin\ForumResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

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