<?php

namespace Tapp\FilamentForum\Filament\Resources\Forums;

use BackedEnum;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Tapp\FilamentForum\Filament\Resources\Forums\Pages\CreateForum;
use Tapp\FilamentForum\Filament\Resources\Forums\Pages\ListForums;
use Tapp\FilamentForum\Filament\Resources\Forums\Pages\ManageForumPosts;
use Tapp\FilamentForum\Filament\Resources\Forums\Pages\ViewForum;
use Tapp\FilamentForum\Filament\Resources\Forums\Schemas\ForumForm;
use Tapp\FilamentForum\Filament\Tables\Components\ForumCardColumn;
use Tapp\FilamentForum\Models\Forum;

class ForumResource extends Resource
{
    protected static ?string $model = Forum::class;

    /** @phpstan-ignore-next-line */
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    /**
     * Check if this resource should be scoped to a tenant.
     * This is called by Filament to determine if tenant scoping should be applied.
     */
    public static function isScopedToTenant(): bool
    {
        return config('filament-forum.tenancy.enabled', false);
    }

    /**
     * Get the tenant ownership relationship name.
     * This tells Filament which relationship to use for tenant scoping.
     */
    public static function getTenantOwnershipRelationshipName(): string
    {
        if (! config('filament-forum.tenancy.enabled')) {
            return 'tenant';
        }

        return Forum::getTenantRelationshipName();
    }

    public static function getBreadcrumb(): string
    {
        return __('filament-forum::filament-forum.forum.breadcrumb');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-forum::filament-forum.forum.navigation-label');
    }

    public static function form(Schema $schema): Schema
    {
        return ForumForm::configure($schema);
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
            'create' => CreateForum::route('/create'),
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
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        // Check if the user model has the canCreateForum method
        if (method_exists($user, 'canCreateForum')) {
            return $user->canCreateForum();
        }

        // Backward compatibility: return false if method doesn't exist
        // This ensures existing installations don't suddenly allow forum creation
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
