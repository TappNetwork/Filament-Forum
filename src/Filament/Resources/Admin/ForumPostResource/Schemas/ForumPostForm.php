<?php

namespace Tapp\FilamentForum\Filament\Resources\Admin\ForumPostResource\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ForumPostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->id('forumPostForm')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->native(false)
                            ->required(),
                        Textarea::make('description')
                            ->required(),
                    ]),
            ]);
    }
} 