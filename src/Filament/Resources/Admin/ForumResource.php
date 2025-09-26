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

    protected static string|UnitEnum|null $navigationGroup = 'Forums';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $slug = 'forums';

    public static function form(Schema $schema): Schema
    {
        $titleAttribute = config('filament-forum.user.title-attribute');
        $searchResultsUsing = config('filament-forum.user.search-results-using');
        $optionLabelUsing = config('filament-forum.user.option-label-using');

        $ownerSelect = Select::make('owner_id')
            ->relationship(
                name: 'owner',
                titleAttribute: $titleAttribute
            )
            ->getOptionLabelFromRecordUsing(function (Model $record) use ($titleAttribute) {
                return $record->{$titleAttribute};
            });

        // Add custom search functionality if provided
        if ($searchResultsUsing && is_callable($searchResultsUsing)) {
            $ownerSelect = $ownerSelect
                ->searchable()
                ->getSearchResultsUsing($searchResultsUsing);

            // Add custom option label if provided
            if ($optionLabelUsing && is_callable($optionLabelUsing)) {
                $ownerSelect = $ownerSelect->getOptionLabelUsing($optionLabelUsing);
            }
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
        $searchResultsUsing = config('filament-forum.user.search-results-using');
        $optionLabelUsing = config('filament-forum.user.option-label-using');

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

        // Add custom search functionality if provided
        if ($searchResultsUsing && is_callable($searchResultsUsing)) {
            $ownerFilter = $ownerFilter->getSearchResultsUsing($searchResultsUsing);

            // Add custom option label if provided
            if ($optionLabelUsing && is_callable($optionLabelUsing)) {
                $ownerFilter = $ownerFilter->getOptionLabelUsing($optionLabelUsing);
            }
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
