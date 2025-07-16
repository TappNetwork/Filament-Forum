<?php

namespace Tapp\FilamentForum\Filament\Resources\ForumResource\Resources\ForumPosts\Schemas;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;

class ForumPostForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('description')
                            ->required()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
} 