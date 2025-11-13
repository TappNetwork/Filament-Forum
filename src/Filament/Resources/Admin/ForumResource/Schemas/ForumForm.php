<?php

namespace Tapp\FilamentForum\Filament\Resources\Admin\ForumResource\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class ForumForm
{
    public static function configure(Schema $schema): Schema
    {
        $titleAttribute = config('filament-forum.user.title-attribute');
        $userModel = config('filament-forum.user.model', 'App\\Models\\User');

        $ownerSelect = Select::make('owner_id')
            ->relationship(
                name: 'owner',
                titleAttribute: $titleAttribute
            )
            ->getOptionLabelFromRecordUsing(function (Model $record) use ($titleAttribute) {
                return $record->{$titleAttribute};
            })
            ->searchable()
            ->label(__('filament-forum::filament-forum.forum.form.label.owner'));

        // Add custom search functionality if User model has the trait and implements custom methods
        if (class_exists($userModel) && method_exists($userModel, 'hasCustomForumSearch') && $userModel::hasCustomForumSearch()) {
            $ownerSelect = $ownerSelect
                ->getSearchResultsUsing(fn (string $search): array => $userModel::getForumSearchResults($search) ?? [])
                ->getOptionLabelUsing(fn ($value): ?string => $userModel::getForumOptionLabel($value));
        }

        return $schema
            ->components([
                Section::make()
                    ->id('forumForm')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label(__('filament-forum::filament-forum.forum.form.label.name'))
                            ->required()
                            ->maxLength(255),
                        $ownerSelect,
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
