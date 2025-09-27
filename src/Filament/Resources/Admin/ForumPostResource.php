<?php

namespace Tapp\FilamentForum\Filament\Resources\Admin;

use App\Models\User;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\Commentions\Filament\Actions\CommentsAction;
use Kirschbaum\Commentions\Filament\Infolists\Components\CommentsEntry;
use Tapp\FilamentForum\Filament\Resources\Admin\ForumPostResource\Pages\CreateForumPost;
use Tapp\FilamentForum\Filament\Resources\Admin\ForumPostResource\Pages\EditForumPost;
use Tapp\FilamentForum\Filament\Resources\Admin\ForumPostResource\Pages\ListForumPosts;
use Tapp\FilamentForum\Filament\Resources\Admin\ForumPostResource\Pages\ViewForumPost;
use Tapp\FilamentForum\Models\ForumPost;
use UnitEnum;

class ForumPostResource extends Resource
{
    protected static ?string $model = ForumPost::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $slug = 'forum-posts';

    public static function getNavigationGroup(): ?string
    {
        return config('filament-forum.admin.navigation-group');
    }

    public static function infolist(Schema $schema): Schema
    {
        $titleAttribute = config('filament-forum.user.title-attribute');

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
                                TextEntry::make("user.{$titleAttribute}")
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
        $titleAttribute = config('filament-forum.user.title-attribute');
        $userModel = config('filament-forum.user.model', 'App\\Models\\User');

        $userSelect = Select::make('user_id')
            ->label('User')
            ->native(false)
            ->required()
            ->relationship(
                name: 'user',
                titleAttribute: $titleAttribute
            )
            ->getOptionLabelFromRecordUsing(function (Model $record) use ($titleAttribute) {
                return $record->{$titleAttribute};
            })
            ->searchable();

        // Add custom search functionality if User model has the trait and implements custom methods
        if (class_exists($userModel) && method_exists($userModel, 'hasCustomForumSearch') && $userModel::hasCustomForumSearch()) {
            $userSelect = $userSelect
                ->getSearchResultsUsing(fn (string $search): array => $userModel::getForumSearchResults($search) ?? [])
                ->getOptionLabelUsing(fn ($value): ?string => $userModel::getForumOptionLabel($value));
        }

        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Select::make('forum_id')
                    ->label('Forum')
                    ->native(false)
                    ->required()
                    ->relationship(
                        name: 'forum',
                        titleAttribute: 'name'
                    ),
                $userSelect,
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        $titleAttribute = config('filament-forum.user.title-attribute');

        return $table
            ->columns([
                TextColumn::make("user.{$titleAttribute}")
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
                    EditAction::make(),
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
