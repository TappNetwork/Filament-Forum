<?php

namespace Tapp\FilamentForum\Filament\Resources\Admin;

use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Tapp\FilamentForum\Filament\Resources\Admin\ForumPostResource\Pages\ListForumPosts;
use Tapp\FilamentForum\Filament\Resources\Admin\ForumPostResource\Pages\CreateForumPost;
use Tapp\FilamentForum\Filament\Resources\Admin\ForumPostResource\Pages\EditForumPost;
use Tapp\FilamentForum\Filament\Resources\Admin\ForumPostResource\Pages\ViewForumPost;
use Tapp\FilamentForum\Filament\Resources\Admin\ForumPostResource\Schemas\ForumPostForm;
use Tapp\FilamentForum\Filament\Resources\Admin\ForumPostResource\Schemas\ForumPostInfolist;
use Tapp\FilamentForum\Models\ForumPost;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Kirschbaum\Commentions\Filament\Actions\CommentsAction;
use Kirschbaum\Commentions\Filament\Infolists\Components\CommentsEntry;
use UnitEnum;
use BackedEnum;

class ForumPostResource extends Resource
{
    protected static ?string $model = ForumPost::class;

    protected static string | UnitEnum | null $navigationGroup = 'Forums';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $slug = 'forum-posts';

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make([
                    'default' => 1,
                    'sm' => 3,
                ])
                    ->schema([
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
                            ->mentionables(fn (Model $record) => User::all()),
                    ]),
            ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
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
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make()
                        ->visible(fn (ForumPost $record): bool => $record->canBeEditedBy()),
                    CommentsAction::make()
                        ->mentionables(User::all()),
                    DeleteAction::make(),
                ])
                    ->tooltip('Actions'),
            ], position: RecordActionsPosition::AfterColumns)
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
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