<?php

namespace Tapp\FilamentForum\Filament\Resources\ForumResource\Resources\ForumPosts\Tables;

use Tapp\FilamentForum\Filament\Tables\Components\ForumPostCardColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Filament\Support\Enums\Alignment;

class ForumPostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->modifyQueryUsing(function (Builder $query) {
                return $query->with(['user']);
            })
            ->columns([
                Stack::make([
                    ForumPostCardColumn::make('name'),
                ])->alignment(Alignment::End)
                ->space(1),
            ])
            ->filters([
                //
            ])
            ->contentGrid([
                'default' => 1,
                'sm' => 1,
            ])
            ->filters([
                //
            ])
            ->recordActions([
                //Tables\Actions\ViewAction::make(),
                EditAction::make()
                    ->icon('heroicon-m-pencil-square')
                    ->iconButton(),
            ])
            ->toolbarActions([
                //Tables\Actions\BulkActionGroup::make([
                //    Tables\Actions\DeleteBulkAction::make(),
                //]),
            ]);
    }
} 