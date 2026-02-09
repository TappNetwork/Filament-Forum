<?php

namespace Tapp\FilamentForum\Filament\Resources\Forums\Pages;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
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

    public function deleteAction(): DeleteAction
    {
        return DeleteAction::make()
            ->requiresConfirmation()
            ->modalHeading(__('filament-forum::filament-forum.forum.delete.modal.heading'))
            ->modalDescription(__('filament-forum::filament-forum.forum.delete.modal.description'))
            ->successNotificationTitle(__('filament-forum::filament-forum.forum.delete.notification'))
            ->iconButton()
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->tooltip(__('filament-forum::filament-forum.forum.delete.tooltip'))
            ->size('sm')
            ->extraAttributes(['class' => 'p-1'])
            ->record(fn (array $arguments) => Forum::find($arguments['record']))
            ->visible(
                fn (array $arguments) => auth()->check() &&
                Forum::find($arguments['record'])?->owner_id === auth()->id() &&
                Forum::find($arguments['record'])?->forumPosts()->count() === 0
            );
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn (): bool => static::getResource()::canCreate()),
        ];
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
