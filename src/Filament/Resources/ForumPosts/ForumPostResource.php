<?php

namespace Tapp\FilamentForum\Filament\Resources\ForumPosts;

use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Tapp\FilamentForum\Filament\Resources\ForumPosts\Pages\ListForumPosts;
use Tapp\FilamentForum\Filament\Resources\ForumPosts\Pages\EditForumPost;
use Tapp\FilamentForum\Filament\Resources\ForumPosts\Pages\ViewForumPost;
use Tapp\FilamentForum\Filament\Resources\ForumPosts\Pages\CreateForumPost;
use Tapp\FilamentForum\Models\ForumPost;
use Kirschbaum\Commentions\Filament\Actions\CommentsAction;
use Kirschbaum\Commentions\Filament\Infolists\Components\CommentsEntry;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use BackedEnum;
use UnitEnum;
use Tapp\FilamentForum\Filament\Resources\ForumPosts\Schemas\ForumPostForm;
use Tapp\FilamentForum\Filament\Resources\ForumPosts\Schemas\ForumPostInfolist;
use Tapp\FilamentForum\Filament\Resources\ForumPosts\Tables\ForumPostsTable;

class ForumPostResource extends Resource
{
    protected static ?string $model = ForumPost::class;
    protected static string|UnitEnum|null $navigationGroup = 'Forum';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    public static function getFormSchema(): array
    {
        return [
            TextInput::make('name')
                ->required()
                ->maxLength(255),
            Select::make('user_id')
                ->relationship('user', 'name')
                ->searchable()
                ->native(false)
                ->required(),
            Textarea::make('description')
                ->required()
                ->columnSpanFull(),
        ];
    }

    public static function getInfolistSchema(): array
    {
        return [
            Grid::make([
                'default' => 1,
                'sm' => 3,
            ])->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('description')
                            ->hiddenLabel()
                            ->columnSpanFull(),
                    ])
                    ->columnSpan(2),
                Section::make()
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('Poster'),
                        TextEntry::make('created_at')
                            ->hiddenLabel()
                            ->since(),
                    ])
                    ->columnSpan(1),
            ]),
            Section::make('Comments')
                ->schema([
                    CommentsEntry::make('comments')
                        ->mentionables(fn ($record) => app(config('auth.providers.users.model'))::all()),
                ]),
        ];
    }

    public static function getTableSchema(): array
    {
        return [
            TextColumn::make('user.name')
                ->sortable(),
            TextColumn::make('name')
                ->label('Question')
                ->sortable()
                ->searchable(),
            TextColumn::make('description')
                ->sortable()
                ->limit(50)
                ->tooltip(function (TextColumn $column): ?string {
                    $state = $column->getState();
                    if (strlen($state) <= $column->getCharacterLimit()) {
                        return null;
                    }
                    // Only render the tooltip if the column content exceeds the length limit.
                    return $state;
                }),
            TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('updated_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }

    public static function getTableActions(): array
    {
        return [
            ActionGroup::make([
                ViewAction::make(),
                EditAction::make(),
                CommentsAction::make()
                    ->mentionables(fn () => app(config('auth.providers.users.model'))::all()),
                DeleteAction::make(),
            ])->tooltip('Actions'),
        ];
    }

    public static function getTableBulkActions(): array
    {
        return [
            BulkActionGroup::make([
                DeleteBulkAction::make(),
            ]),
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