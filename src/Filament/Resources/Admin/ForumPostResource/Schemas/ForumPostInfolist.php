<?php

namespace Tapp\FilamentForum\Filament\Resources\Admin\ForumPostResource\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Kirschbaum\Commentions\Filament\Infolists\Components\CommentsEntry;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class ForumPostInfolist
{
    public static function configure(Schema $schema): Schema
    {
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
                                TextEntry::make('user.name')
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
} 