<?php

namespace Tapp\FilamentForum\Filament\Resources\Admin\ForumContentReportResource\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Tapp\FilamentForum\Filament\Resources\Admin\ForumContentReportResource;

class ViewForumContentReport extends ViewRecord
{
    protected static string $resource = ForumContentReportResource::class;

    /**
     * @return array<int, Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            ForumContentReportResource::resolveAction(),
            ForumContentReportResource::dismissAction(),
        ];
    }
}
