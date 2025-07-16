<?php

namespace Tapp\FilamentForum\Filament\Resources\ForumPostsResource\Schemas;

use Tapp\FilamentForum\Models\ForumPost;
use Tapp\FilamentForum\Models\User;
use Filament\Actions\Action;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\Commentions\Filament\Infolists\Components\CommentsEntry;

class ForumPostInfolist
{
    public static function configure(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make(fn (ForumPost $record) => $record->user->name.' - '.$record->created_at->diffForHumans())
                    ->headerActions([
                        Action::make('favorite')
                            ->iconButton()
                            ->icon(fn (ForumPost $record) => $record->isFavorite() ? 'heroicon-s-star' : 'heroicon-o-star')
                            ->action(fn (ForumPost $record) => $record->toggleFavorite()),
                        Action::make('share')
                            ->icon('heroicon-o-share')
                            ->action(function ($livewire) {
                                $livewire->js(
                                    'window.navigator.clipboard.writeText(window.location.href);
                                    $tooltip("'.__('Copied to clipboard').'", { timeout: 1500 });'
                                );
                            }),
                    ])
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 3,
                        ])
                            ->schema([
                                TextEntry::make('description')
                                    ->hiddenLabel()
                                    ->columnSpanFull(),
                            ]),

                        Section::make('Comments')
                            ->schema([
                                CommentsEntry::make('comments')
                                    ->mentionables(fn (Model $record) => User::all()),
                            ]),
                    ])
            ]);
    }
} 