<?php

namespace Tapp\FilamentForum\Filament\Resources\Forums\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ForumForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->id('forumForm')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label(__('filament-forum::filament-forum.forum.form.label.name'))
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->label(__('filament-forum::filament-forum.forum.form.label.description'))
                            ->required()
                            ->columnSpanFull(),
                        SpatieMediaLibraryFileUpload::make('image')
                            ->label(__('filament-forum::filament-forum.forum.form.label.image'))
                            ->collection('images')
                            ->columnSpanFull(),
                        Checkbox::make('is_hidden')
                            ->label('Hidden Forum')
                            ->helperText('If checked, only assigned users can view this forum. If unchecked, all logged in users can view it.')
                            ->live(),
                    ]),
            ]);
    }
}
