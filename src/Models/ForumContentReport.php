<?php

namespace Tapp\FilamentForum\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Tapp\FilamentForum\Events\ForumContentReportDismissed;
use Tapp\FilamentForum\Events\ForumContentReported;
use Tapp\FilamentForum\Events\ForumContentReportResolved;
use Tapp\FilamentForum\Models\Traits\BelongsToTenant;

/**
 * @property string $status
 */
class ForumContentReport extends Model
{
    use BelongsToTenant;

    public const STATUS_PENDING = 'pending';

    public const STATUS_RESOLVED = 'resolved';

    public const STATUS_DISMISSED = 'dismissed';

    protected $guarded = [];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => __('filament-forum::filament-forum.reports.status.pending'),
            self::STATUS_RESOLVED => __('filament-forum::filament-forum.reports.status.resolved'),
            self::STATUS_DISMISSED => __('filament-forum::filament-forum.reports.status.dismissed'),
        ];
    }

    public function reportable(): MorphTo
    {
        return $this->morphTo();
    }

    public function reporter(): MorphTo
    {
        return $this->morphTo();
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'reviewed_by_id');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeResolved(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_RESOLVED);
    }

    public function scopeDismissed(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_DISMISSED);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function resolve($reviewer, ?string $note = null): void
    {
        $this->updateReviewState(self::STATUS_RESOLVED, $reviewer, $note);

        ForumContentReportResolved::dispatch($this);
    }

    public function dismiss($reviewer, ?string $note = null): void
    {
        $this->updateReviewState(self::STATUS_DISMISSED, $reviewer, $note);

        ForumContentReportDismissed::dispatch($this);
    }

    public function markReported(): void
    {
        ForumContentReported::dispatch($this);
    }

    protected function updateReviewState(string $status, $reviewer, ?string $note = null): void
    {
        $this->forceFill([
            'status' => $status,
            'reviewed_by_id' => $reviewer?->getKey(),
            'reviewed_at' => now(),
            'resolution_note' => $note,
        ])->save();
    }
}
