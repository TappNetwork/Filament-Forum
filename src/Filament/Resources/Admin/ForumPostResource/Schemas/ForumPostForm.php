<?php

namespace Tapp\FilamentForum\Filament\Resources\Admin\ForumPostResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class ForumPostForm
{
    public static function configure(Schema $schema): Schema
    {
        $titleAttribute = config('filament-forum.user.title-attribute');
        $userModel = config('filament-forum.user.model', 'App\\Models\\User');

        $userSelect = Select::make('user_id')
            ->relationship(
                name: 'user',
                titleAttribute: $titleAttribute
            )
            ->getOptionLabelFromRecordUsing(function (Model $record) use ($titleAttribute) {
                return $record->{$titleAttribute};
            })
            ->searchable()
            ->label(__('filament-forum::filament-forum.forum-post.form.label.user'))
            ->native(false)
            ->required();

        // Add custom search functionality if User model has the trait and implements custom methods
        if (class_exists($userModel) && method_exists($userModel, 'hasCustomForumSearch') && $userModel::hasCustomForumSearch()) {
            $userSelect = $userSelect
                ->getSearchResultsUsing(fn (string $search): array => $userModel::getForumSearchResults($search) ?? [])
                ->getOptionLabelUsing(fn ($value): ?string => $userModel::getForumOptionLabel($value));
        }

        return $schema
            ->components([
                Section::make()
                    ->id('forumPostForm')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label(__('filament-forum::filament-forum.forum-post.form.label.name'))
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Select::make('forum_id')
                            ->label(__('filament-forum::filament-forum.forum-post.form.label.forum'))
                            ->native(false)
                            ->required()
                            ->relationship(
                                name: 'forum',
                                titleAttribute: 'name'
                            ),
                        $userSelect,
                        Textarea::make('description')
                            ->label(__('filament-forum::filament-forum.forum-post.form.label.description'))
                            ->required()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
