<?php

namespace Tapp\FilamentForum\Filament\Resources\ForumPosts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Table;
use Filament\Support\Enums\Alignment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Tapp\FilamentForum\Filament\Tables\Components\ForumPostCardColumn;
use Tapp\FilamentForum\Models\ForumPost;

class ForumPostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->modifyQueryUsing(function (Builder $query) {
                return $query->with(['user', 'forum']);
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
                    ->iconButton()
                    ->visible(fn (ForumPost $record): bool => Auth::check() && $record->user_id === Auth::id()),
            ])
            ->toolbarActions([
                //Tables\Actions\BulkActionGroup::make([
                //    Tables\Actions\DeleteBulkAction::make(),
                //]),
            ]);
    }
}
