<?php

namespace Tapp\FilamentForum\Filament\Resources\Admin\ForumResource\Tables;

use App\Models\User;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ForumsTable
{
    public static function configure(Table $table): Table
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
                    ->label(__('filament-forum::filament-forum.forum.table.label.image'))
                    ->collection('images'),
                TextColumn::make("owner.{$titleAttribute}")
                    ->label(__('filament-forum::filament-forum.forum.table.label.owner'))
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('filament-forum::filament-forum.forum.table.label.name'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('description')
                    ->label(__('filament-forum::filament-forum.forum.table.label.description'))
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
                TextColumn::make('is_hidden')
                    ->label('Access')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Hidden' : 'Public')
                    ->color(fn (bool $state): string => $state ? 'warning' : 'success')
                    ->icon(fn (bool $state): string => $state ? 'heroicon-o-lock-closed' : 'heroicon-o-globe-alt'),
                TextColumn::make('created_at')
                    ->label(__('filament-forum::filament-forum.forum.table.label.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('filament-forum::filament-forum.forum.table.label.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                $ownerFilter,
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
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
}
