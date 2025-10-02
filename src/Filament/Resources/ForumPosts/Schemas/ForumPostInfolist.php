<?php

namespace Tapp\FilamentForum\Filament\Resources\ForumPosts\Schemas;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\Commentions\Filament\Infolists\Components\CommentsEntry;
use Tapp\FilamentForum\Models\ForumPost;

class ForumPostInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(fn (ForumPost $record) => $record->user->name.' - '.$record->created_at->diffForHumans().($record->hasBeenEdited() ? ' ('.__('filament-forum::filament-forum.forum-post.edited').')' : ''))
                    ->headerActions([
                        Action::make('favorite')
                            ->iconButton()
                            ->tooltip(__('filament-forum::filament-forum.forum-post.toggle-favorite'))
                            ->icon(fn (ForumPost $record) => $record->isFavorite() ? 'heroicon-s-star' : 'heroicon-o-star')
                            ->action(fn (ForumPost $record) => $record->toggleFavorite()),
                        Action::make('share')
                            ->icon('heroicon-o-share')
                            ->action(function ($livewire) {
                                $livewire->js(
                                    'if (navigator.clipboard) {
                                        navigator.clipboard.writeText(window.location.href).then(function() {
                                            $tooltip("Copied to clipboard", { timeout: 1500 });
                                        }).catch(function() {
                                            $tooltip("Failed to copy", { timeout: 1500 });
                                        });
                                    } else {
                                        var textArea = document.createElement("textarea");
                                        textArea.value = window.location.href;
                                        document.body.appendChild(textArea);
                                        textArea.select();
                                        try {
                                            document.execCommand("copy");
                                            $tooltip("Copied to clipboard", { timeout: 1500 });
                                        } catch (err) {
                                            $tooltip("Failed to copy", { timeout: 1500 });
                                        }
                                        document.body.removeChild(textArea);
                                    }'
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
                                    ->label(__('filament-forum::filament-forum.forum-post.infolist.label.name'))
                                    ->hiddenLabel()
                                    ->columnSpanFull(),
                            ]),

                        Section::make('Comments')
                            ->schema([
                                CommentsEntry::make('comments')
                                    ->hiddenLabel()
                                    ->mentionables(fn (Model $record) => User::all()),
                            ]),
                    ]),
            ]);
    }
}
