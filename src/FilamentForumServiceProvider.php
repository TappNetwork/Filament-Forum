<?php

declare(strict_types=1);

namespace Tapp\FilamentForum;

use Filament\Facades\Filament;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

// The following linter errors may appear if Filament is not installed in the dev environment, but are not actual code issues in a Filament app.
// @phpstan-ignore-next-line

class FilamentForumServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-forum')
            ->hasConfigFile()
            ->hasViews()
            ->hasAssets()
            ->hasMigrations([
                'create_forums_table',
                'create_forum_posts_table',
                'create_favorite_forum_post_table',
                'create_forum_post_views_table',
                'create_forum_comments_table',
                'create_forum_comment_reactions_table',
            ])
            ->hasTranslations();
    }

    public function packageBooted()
    {
        // Register Livewire components
        Livewire::component('tapp.filament-forum.forum-comments', \Tapp\FilamentForum\Livewire\ForumComments::class);
        Livewire::component('tapp.filament-forum.forum-comment-reactions', \Tapp\FilamentForum\Livewire\ForumCommentReactions::class);

        // Register ForumResource in your panel provider to add it to navigation:
        // In your PanelProvider:
        // public function resources(): array {
        //     return [
        //         \Tapp\FilamentForum\Filament\Resources\ForumResource::class,
        //         // ...other resources
        //     ];
        // }
    }
}
