<?php

namespace Tapp\FilamentForum\Models\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Tapp\FilamentForum\Models\ForumContentReport;

trait CanReportForumContent
{
    public function contentReports(): MorphMany
    {
        return $this->morphMany(ForumContentReport::class, 'reportable');
    }

    public function pendingContentReports(): MorphMany
    {
        return $this->contentReports()->where('status', ForumContentReport::STATUS_PENDING);
    }

    public function reportContent($reporter, ?string $reason = null, ?string $details = null): ForumContentReport
    {
        $values = [
            'reason' => $reason,
            'details' => $details,
        ];

        if (config('filament-forum.tenancy.enabled')) {
            $tenantColumnName = static::getTenantColumnName();
            $tenantId = $this->{$tenantColumnName};

            if ($tenantId) {
                $values[$tenantColumnName] = $tenantId;
            }
        }

        /** @var ForumContentReport $report */
        $report = $this->contentReports()->updateOrCreate(
            [
                'reporter_id' => $reporter->getKey(),
                'reporter_type' => get_class($reporter),
                'status' => ForumContentReport::STATUS_PENDING,
            ],
            $values
        );

        $report->markReported();

        return $report;
    }

    public function hasPendingReportBy($reporter): bool
    {
        return $this->pendingContentReports()
            ->where('reporter_id', $reporter->getKey())
            ->where('reporter_type', get_class($reporter))
            ->exists();
    }

    public function getPendingReportsCountAttribute(): int
    {
        return $this->pendingContentReports()->count();
    }
}
