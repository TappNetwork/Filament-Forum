<?php

namespace Tapp\FilamentForum\Filament\Resources\Admin;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Tapp\FilamentForum\Filament\Resources\Admin\ForumResource\Pages\CreateForum;
use Tapp\FilamentForum\Filament\Resources\Admin\ForumResource\Pages\EditForum;
use Tapp\FilamentForum\Filament\Resources\Admin\ForumResource\Pages\ListForums;
use Tapp\FilamentForum\Models\Forum;
use UnitEnum;

class ForumResource extends Resource
{
    protected static ?string $model = Forum::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $slug = 'forums';

    public static function getNavigationGroup(): ?string
    {
        return config('filament-forum.admin.navigation-group');
    }

    public static function form(Schema $schema): Schema
    {
        $titleAttribute = config('filament-forum.user.title-attribute');
        $userModel = config('filament-forum.user.model', 'App\\Models\\User');

        $ownerSelect = Select::make('owner_id')
            ->relationship(
                name: 'owner',
                titleAttribute: $titleAttribute
            )
            ->getOptionLabelFromRecordUsing(function (Model $record) use ($titleAttribute) {
                return $record->{$titleAttribute};
            })
            ->searchable();

        // Add custom search functionality if User model has the trait and implements custom methods
        if (class_exists($userModel) && method_exists($userModel, 'hasCustomForumSearch') && $userModel::hasCustomForumSearch()) {
            $ownerSelect = $ownerSelect
                ->getSearchResultsUsing(fn (string $search): array => $userModel::getForumSearchResults($search) ?? [])
                ->getOptionLabelUsing(fn ($value): ?string => $userModel::getForumOptionLabel($value));
        }

        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                $ownerSelect,
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                SpatieMediaLibraryFileUpload::make('image')
                    ->collection('images')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        $titleAttribute = config('filament-forum.user.title-attribute');
        $userModel = config('filament-forum.user.model', 'App\\Models\\User');

        $ownerFilter = SelectFilter::make('owner_id')
            ->label('Owner')
            ->relationship(
                name: 'owner',
                titleAttribute: $titleAttribute
            )
            ->getOptionLabelFromRecordUsing(function (Model $record) use ($titleAttribute) {
                return $record->{$titleAttribute};
            })
            ->searchable();

        // Add custom search functionality if User model has the trait and implements custom methods
        if (class_exists($userModel) && method_exists($userModel, 'hasCustomForumSearch') && $userModel::hasCustomForumSearch()) {
            $ownerFilter = $ownerFilter
                ->getSearchResultsUsing(fn (string $search): array => $userModel::getForumSearchResults($search) ?? [])
                ->getOptionLabelUsing(fn ($value): ?string => $userModel::getForumOptionLabel($value));
        }

        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('image')
                    ->collection('images'),
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make("owner.{$titleAttribute}")
                    ->sortable(),
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
                $ownerFilter,
            ])
            ->recordActions([
                EditAction::make(),
            ])
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
            'index' => ListForums::route('/'),
            'create' => CreateForum::route('/create'),
            'edit' => EditForum::route('/{record}/edit'),
        ];
    }
}
