<?php

namespace Tapp\FilamentForum\Filament\Resources\Forums\Pages;

use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Tapp\FilamentForum\Filament\Resources\ForumPosts\ForumPostResource;
use Tapp\FilamentForum\Filament\Resources\ForumPosts\Tables\ForumPostsTable;
use Tapp\FilamentForum\Filament\Resources\Forums\ForumResource;
use Tapp\FilamentForum\Models\ForumPost;

class ManageForumPosts extends ManageRelatedRecords
{
    protected static string $resource = ForumResource::class;

    protected static string $relationship = 'forumPosts';

    /** @phpstan-ignore-next-line */
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $relatedResource = ForumPostResource::class;

    public function getTitle(): string|Htmlable
    {
        return config('filament-forum.frontend.forum-posts.title');
    }

    public function getBreadcrumb(): string
    {
        return config('filament-forum.frontend.forum-posts.breadcrumb');
    }

    public function table(Table $table): Table
    {
        return ForumPostsTable::configure($table)
            ->headerActions([
                CreateAction::make(),
            ]);
    }

    public function toggleFavorite($recordId)
    {
        $forumPostrecord = ForumPost::find($recordId);
        $forumPostrecord->toggleFavorite();

        $this->dispatch('favorite-toggled', recordId: $recordId, isFavorite: $forumPostrecord->isFavorite());
    }
}
