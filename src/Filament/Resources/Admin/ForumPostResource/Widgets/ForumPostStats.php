<?php

namespace Tapp\FilamentForum\Filament\Resources\Admin\ForumPostResource\Widgets;

use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Tapp\FilamentForum\Filament\Resources\Admin\ForumPostResource\Pages\ListForumPosts;
use Tapp\FilamentForum\Models\ForumComment;

class ForumPostStats extends BaseWidget
{
    use InteractsWithPageTable;

    protected static bool $isLazy = false;

    protected ?string $pollingInterval = null;

    protected function getTablePage(): string
    {
        return ListForumPosts::class;
    }

    protected function getStats(): array
    {
        $query = $this->getPageTableQuery();

        $postIds = (clone $query)->pluck('forum_posts.id');

        $totalPosts = (clone $query)->count();

        $totalFavorited = DB::table('favorite_forum_post')
            ->whereIn('forum_post_id', $postIds)
            ->distinct('forum_post_id')
            ->count('forum_post_id');

        $totalComments = ForumComment::query()
            ->whereIn('forum_post_id', $postIds)
            ->count();

        return [
            Stat::make(__('Posts'), number_format($totalPosts))
                ->description(__('Total forum posts'))
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('primary'),
            Stat::make(__('Favorited'), number_format($totalFavorited))
                ->description(__('Posts with favorites'))
                ->descriptionIcon('heroicon-m-heart')
                ->color('danger'),
            Stat::make(__('Comments'), number_format($totalComments))
                ->description(__('Total comments'))
                ->descriptionIcon('heroicon-m-chat-bubble-bottom-center-text')
                ->color('success'),
        ];
    }
}
