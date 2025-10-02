<?php

namespace Tapp\FilamentForum\Filament\Resources\ForumPosts;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Tapp\FilamentForum\Filament\Resources\ForumPosts\Pages\CreateForumPost;
use Tapp\FilamentForum\Filament\Resources\ForumPosts\Pages\EditForumPost;
use Tapp\FilamentForum\Filament\Resources\ForumPosts\Pages\ViewForumPost;
use Tapp\FilamentForum\Filament\Resources\ForumPosts\Schemas\ForumPostForm;
use Tapp\FilamentForum\Filament\Resources\ForumPosts\Schemas\ForumPostInfolist;
use Tapp\FilamentForum\Filament\Resources\ForumPosts\Tables\ForumPostsTable;
use Tapp\FilamentForum\Filament\Resources\Forums\ForumResource;
use Tapp\FilamentForum\Models\ForumPost;

class ForumPostResource extends Resource
{
    protected static ?string $model = ForumPost::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $parentResource = ForumResource::class;

    public static function getPluralLabel(): ?string
    {
        return __('filament-forum::filament-forum.forum-post.plural-label');
    }

    public static function getLabel(): ?string
    {
        return __('filament-forum::filament-forum.forum-post.label');
    }

    public static function form(Schema $schema): Schema
    {
        return ForumPostForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ForumPostInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ForumPostsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getSlug(?\Filament\Panel $panel = null): string
    {
        return config('filament-forum.forum-post.slug');
    }

    public static function getPages(): array
    {
        return [
            'create' => CreateForumPost::route('/create'),
            'view' => ViewForumPost::route('/{record}'),
            'edit' => EditForumPost::route('/{record}/edit'),
        ];
    }
}
