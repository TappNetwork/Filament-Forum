<?php

namespace Tapp\FilamentForum\Filament\Resources\Admin\ForumPostResource\Tables;

use App\Models\User;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;
use Kirschbaum\Commentions\Filament\Actions\CommentsAction;

class ForumPostsTable
{
    public static function configure(Table $table): Table
    {
        $titleAttribute = config('filament-forum.user.title-attribute');

        return $table
            ->columns([
                TextColumn::make("user.{$titleAttribute}")
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('filament-forum::filament-forum.forum-post.table.label.name'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('description')
                    ->label(__('filament-forum::filament-forum.forum-post.table.label.description'))
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
                    ->label(__('filament-forum::filament-forum.forum-post.table.label.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('filament-forum::filament-forum.forum-post.table.label.updated-at'))
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
}
