<?php

namespace Tapp\FilamentForum\Filament\Resources\ForumPosts\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ForumPostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->id('forumPostForm')
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->label(__('filament-forum::filament-forum.forum-post.form.label.name'))
                            ->required()
                            ->maxLength(255),
                        Textarea::make('description')
                            ->label(__('filament-forum::filament-forum.forum-post.form.label.description'))
                            ->required(),
                    ]),
            ]);
    }
}
