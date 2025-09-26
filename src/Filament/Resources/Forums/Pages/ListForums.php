<?php

namespace Tapp\FilamentForum\Filament\Resources\Forums\Pages;

use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Tapp\FilamentForum\Filament\Resources\Forums\ForumResource;

class ListForums extends ListRecords
{
    protected static string $resource = ForumResource::class;

    public function getTitle(): string|Htmlable
    {
        return config('filament-forum.frontend.forum.title');
    }
}
