<?php

namespace Tapp\FilamentForum\Filament\Resources\Forums\Pages;

use Tapp\FilamentForum\Filament\Resources\Forums\ForumsResource;
use Tapp\FilamentForum\Filament\Resources\ForumPosts\ForumPostsResource;
use Tapp\FilamentForum\Models\ForumPost;
use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Table;

class ManageForumPosts extends ManageRelatedRecords
{
    protected static string $resource = ForumsResource::class;

    protected static string $relationship = 'forumPosts';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $relatedResource = ForumPostsResource::class;

    public function table(Table $table): Table
    {
        return $table
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