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
use Tapp\FilamentForum\Filament\Resources\Admin\ForumPostResource\Widgets\ForumPostStats;
use Tapp\FilamentForum\Models\ForumPost;

class ForumPostResource extends Resource
{
    protected static ?string $model = ForumPost::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $slug = 'forum-posts';

    /**
     * Check if this resource should be scoped to a tenant.
     * This is called by Filament to determine if tenant scoping should be applied.
     */
    public static function isScopedToTenant(): bool
    {
        return config('filament-forum.tenancy.enabled', false);
    }

    /**
     * Get the tenant ownership relationship name.
     * This tells Filament which relationship to use for tenant scoping.
     */
    public static function getTenantOwnershipRelationshipName(): string
    {
        if (! config('filament-forum.tenancy.enabled')) {
            return 'tenant';
        }

        return ForumPost::getTenantRelationshipName();
    }

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

    public static function getWidgets(): array
    {
        return [
            ForumPostStats::class,
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
