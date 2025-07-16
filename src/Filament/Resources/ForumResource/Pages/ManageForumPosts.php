<?php

namespace Tapp\FilamentForum\Filament\Resources\ForumResource\Pages;

use Tapp\FilamentForum\Filament\Resources\ForumResource;
use Tapp\FilamentForum\Filament\Resources\ForumPostResource;
use Tapp\FilamentForum\Models\ForumPost;
use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Table;

class ManageForumPosts extends ManageRelatedRecords
{
    protected static string $resource = ForumResource::class;

    protected static string $relationship = 'forumPosts';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $relatedResource = ForumPostResource::class;

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