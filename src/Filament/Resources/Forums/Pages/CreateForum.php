<?php

namespace Tapp\FilamentForum\Filament\Resources\Forums\Pages;

use Filament\Resources\Pages\CreateRecord;
use Tapp\FilamentForum\Filament\Resources\Forums\ForumResource;

class CreateForum extends CreateRecord
{
    protected static string $resource = ForumResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Automatically set the owner to the currently logged-in user
        $data['owner_id'] = auth()->id();

        return $data;
    }
}
