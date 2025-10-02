<?php

namespace Tapp\FilamentForum\Filament\Resources\Admin\ForumPostResource\Schemas;

use App\Models\User;
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
                                    ->label(__('filament-forum::filament-forum.forum-post.infolist.label.poster')),
                                TextEntry::make('created_at')
                                    ->hiddenLabel()
                                    ->since(),
                                TextEntry::make('updated_at')
                                    ->label(__('filament-forum::filament-forum.forum-post.infolist.label.last-edited'))
                                    ->since()
                                    ->visible(fn (ForumPost $record) => $record->hasBeenEdited()),
                            ])
                            ->columnSpan(1),
                    ]),

                Section::make('Comments')
                    ->schema([
                        CommentsEntry::make('comments')
                            ->hiddenLabel()
                            ->mentionables(fn (Model $record) => User::all()),
                    ]),
            ]);
    }
}
