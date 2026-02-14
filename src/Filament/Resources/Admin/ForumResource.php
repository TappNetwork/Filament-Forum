<?php

namespace Tapp\FilamentForum\Filament\Resources\Admin;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Tapp\FilamentForum\Filament\Resources\Admin\ForumResource\Pages\CreateForum;
use Tapp\FilamentForum\Filament\Resources\Admin\ForumResource\Pages\EditForum;
use Tapp\FilamentForum\Filament\Resources\Admin\ForumResource\Pages\ListForums;
use Tapp\FilamentForum\Filament\Resources\Admin\ForumResource\Schemas\ForumForm;
use Tapp\FilamentForum\Filament\Resources\Admin\ForumResource\Tables\ForumsTable;
use Tapp\FilamentForum\Filament\Resources\Admin\ForumResource\Widgets\ForumStats;
use Tapp\FilamentForum\Models\Forum;
use Tapp\FilamentForum\RelationManagers\ForumUsersRelationManager;

class ForumResource extends Resource
{
    protected static ?string $model = Forum::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $slug = 'forums';

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

        return Forum::getTenantRelationshipName();
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament-forum::filament-forum.admin.navigation-group');
    }

    public static function form(Schema $schema): Schema
    {
        return ForumForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ForumsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ForumUsersRelationManager::make(),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            ForumStats::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListForums::route('/'),
            'create' => CreateForum::route('/create'),
            'edit' => EditForum::route('/{record}/edit'),
        ];
    }
}
