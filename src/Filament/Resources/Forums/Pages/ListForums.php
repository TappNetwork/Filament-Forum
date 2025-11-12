<?php

namespace Tapp\FilamentForum\Filament\Resources\Forums\Pages;

use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use Tapp\FilamentForum\Filament\Resources\Forums\ForumResource;
use Tapp\FilamentForum\Models\Forum;

class ListForums extends ListRecords
{
    protected static string $resource = ForumResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('filament-forum::filament-forum.forum.title');
    }

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $user = Auth::user();

        if ($user) {
            // Use the accessibleTo scope for better performance
            return Forum::accessibleTo($user);
        }

        // For non-authenticated users, only show public forums
        return Forum::where('is_hidden', false);
    }
}
