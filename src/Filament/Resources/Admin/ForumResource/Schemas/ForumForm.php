<?php

namespace Tapp\FilamentForum\Filament\Resources\Admin\ForumResource\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Schemas\Schema;

class ForumForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->id('forumForm')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Select::make('owner_id')
                            ->relationship('owner', 'name'),
                        Textarea::make('description')
                            ->required(),
                        SpatieMediaLibraryFileUpload::make('image')
                            ->collection('images')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
} 