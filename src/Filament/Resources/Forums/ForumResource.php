<?php

namespace Tapp\FilamentForum\Filament\Resources\Forums;

use BackedEnum;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Tapp\FilamentForum\Filament\Resources\Forums\Pages\ListForums;
use Tapp\FilamentForum\Filament\Resources\Forums\Pages\ManageForumPosts;
use Tapp\FilamentForum\Filament\Resources\Forums\Pages\ViewForum;
use Tapp\FilamentForum\Filament\Tables\Components\ForumCardColumn;
use Tapp\FilamentForum\Models\Forum;

class ForumResource extends Resource
{
    protected static ?string $model = Forum::class;

    /** @phpstan-ignore-next-line */
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    public static function getBreadcrumb(): string
    {
        return __('filament-forum::filament-forum.forum.breadcrumb');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-forum::filament-forum.forum.navigation-label');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->columns([
                Stack::make([
                    ForumCardColumn::make('name'),
                ])->alignment(Alignment::End)
                    ->space(1),
            ])
            ->filters([
                //
            ])
            ->contentGrid([
                'md' => 2,
                'lg' => 3,
            ])
            ->recordActions([
                //
            ]);
    }

    public static function getInfolistSchema(): array
    {
        return [
            Section::make(fn (Forum $record) => $record->name)
                ->schema([
                    TextEntry::make('image'),
                    TextEntry::make('description'),
                    Fieldset::make('')
                        ->extraAttributes(['class' => 'border-none xl:-ml-16'])
                        ->schema([
                            TextEntry::make('forumPosts.name')
                                ->listWithLineBreaks(),
                        ])->columnSpanFull(),
                ])->columns([
                    'lg' => 1,
                    'xl' => 2,
                ]),
        ];
    }

    public static function getPages(): array
    {
        $forumPostSlug = config('filament-forum.forum-post.slug');

        return [
            'index' => ListForums::route('/'),
            // 'view' => ViewForum::route('/{record}'),
            'forum-posts' => ManageForumPosts::route("/{record}/{$forumPostSlug}"),
        ];
    }

    public static function getSlug(?\Filament\Panel $panel = null): string
    {
        return config('filament-forum.forum.slug');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }
}
