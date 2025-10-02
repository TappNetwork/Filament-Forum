<?php

namespace Tapp\FilamentForum\Filament\Resources\Admin;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Tapp\FilamentForum\Filament\Resources\Admin\ForumPostResource\Pages\CreateForumPost;
use Tapp\FilamentForum\Filament\Resources\Admin\ForumPostResource\Pages\EditForumPost;
use Tapp\FilamentForum\Filament\Resources\Admin\ForumPostResource\Pages\ListForumPosts;
use Tapp\FilamentForum\Filament\Resources\Admin\ForumPostResource\Pages\ViewForumPost;
use Tapp\FilamentForum\Filament\Resources\Admin\ForumPostResource\Schemas\ForumPostForm;
use Tapp\FilamentForum\Filament\Resources\Admin\ForumPostResource\Schemas\ForumPostInfolist;
use Tapp\FilamentForum\Filament\Resources\Admin\ForumPostResource\Tables\ForumPostsTable;
use Tapp\FilamentForum\Models\ForumPost;

class ForumPostResource extends Resource
{
    protected static ?string $model = ForumPost::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $slug = 'forum-posts';

    public static function getNavigationGroup(): ?string
    {
        return __('filament-forum::filament-forum.admin.navigation-group');
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

    public static function getPages(): array
    {
        return [
            'index' => ListForumPosts::route('/'),
            'create' => CreateForumPost::route('/create'),
            'edit' => EditForumPost::route('/{record}/edit'),
            'view' => ViewForumPost::route('/{record}'),
        ];
    }
}
