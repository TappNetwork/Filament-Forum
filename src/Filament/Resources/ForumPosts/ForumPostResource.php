<?php

namespace Tapp\FilamentForum\Filament\Resources\ForumPosts;

use Tapp\FilamentForum\Filament\Resources\Forums\ForumResource;
use Tapp\FilamentForum\Filament\Resources\ForumPosts\Pages\CreateForumPost;
use Tapp\FilamentForum\Filament\Resources\ForumPosts\Pages\EditForumPost;
use Tapp\FilamentForum\Filament\Resources\ForumPosts\Pages\ViewForumPost;
use Tapp\FilamentForum\Filament\Resources\ForumPosts\Schemas\ForumPostForm;
use Tapp\FilamentForum\Filament\Resources\ForumPosts\Schemas\ForumPostInfolist;
use Tapp\FilamentForum\Filament\Resources\ForumPosts\Tables\ForumPostsTable;
use Tapp\FilamentForum\Models\ForumPost;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\TextEntry;

use Filament\Tables\Table;
use Filament\Support\Icons\Heroicon;

class ForumPostResource extends Resource
{
    protected static ?string $model = ForumPost::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $parentResource = ForumResource::class;

    public static function form(Schema $schema): Schema
    {
        return ForumPostForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                InfolistSection::make(fn (\Tapp\FilamentForum\Models\ForumPost $record) => $record->user->name.' - '.$record->created_at->diffForHumans())
                    ->schema([
                        TextEntry::make('description')
                            ->hiddenLabel()
                            ->columnSpanFull(),
                    ]),
            ]);
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
            'create' => CreateForumPost::route('/create'),
            'view' => ViewForumPost::route('/{record}'),
            'edit' => EditForumPost::route('/{record}/edit'),
        ];
    }
} 