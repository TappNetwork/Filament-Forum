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
use Tapp\FilamentForum\Models\Forum;

class ForumResource extends Resource
{
    protected static ?string $model = Forum::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $slug = 'forums';

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
            //
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
