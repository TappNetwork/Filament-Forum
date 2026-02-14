<?php

namespace Tapp\FilamentForum\Filament\Resources\Admin\ForumResource\Widgets;

use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Tapp\FilamentForum\Filament\Resources\Admin\ForumResource\Pages\ListForums;

class ForumStats extends BaseWidget
{
    use InteractsWithPageTable;

    protected static bool $isLazy = false;

    protected ?string $pollingInterval = null;

    protected function getTablePage(): string
    {
        return ListForums::class;
    }

    protected function getStats(): array
    {
        $query = $this->getPageTableQuery();

        $forumIds = (clone $query)->pluck('forums.id');

        $totalForums = (clone $query)->count();

        $totalMembers = DB::table('forum_user')
            ->whereIn('forum_id', $forumIds)
            ->distinct('user_id')
            ->count('user_id');

        $hiddenForums = (clone $query)->where('is_hidden', true)->count();

        return [
            Stat::make(__('Forums'), number_format($totalForums))
                ->description(__('Total discussion boards'))
                ->descriptionIcon('heroicon-m-rectangle-stack')
                ->color('primary'),
            Stat::make(__('Members'), number_format($totalMembers))
                ->description(__('Unique members assigned'))
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),
            Stat::make(__('Hidden'), number_format($hiddenForums))
                ->description(__('Private forums'))
                ->descriptionIcon('heroicon-m-eye-slash')
                ->color('warning'),
        ];
    }
}
