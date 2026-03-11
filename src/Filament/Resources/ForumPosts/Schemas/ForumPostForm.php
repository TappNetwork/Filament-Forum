<?php

namespace Tapp\FilamentForum\Filament\Resources\ForumPosts\Schemas;

use Filament\Forms\Components\RichEditor;
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
                        RichEditor::make('description')
                            ->label(__('filament-forum::filament-forum.forum-post.form.label.description'))
                            ->required()
                            ->toolbarButtons([
                                'attachFiles',
                                'blockquote',
                                'bold',
                                'bulletList',
                                'codeBlock',
                                'h2',
                                'h3',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'underline',
                                'undo',
                            ])
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('forum-posts')
                            ->fileAttachmentsVisibility('public')
                            ->maxLength(10000),
                    ]),
            ]);
    }
}
