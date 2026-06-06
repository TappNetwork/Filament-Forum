<?php

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Event;
use Tapp\FilamentForum\Events\ForumContentReportDismissed;
use Tapp\FilamentForum\Events\ForumContentReported;
use Tapp\FilamentForum\Events\ForumContentReportResolved;
use Tapp\FilamentForum\Models\Forum;
use Tapp\FilamentForum\Models\ForumContentReport;
use Tapp\FilamentForum\Models\ForumPost;

beforeEach(function () {
    $this->userModel = config('filament-forum.user.model');
    $this->tenantModel = config('filament-forum.tenancy.model');
});

it('can report a forum post with tenancy enabled', function () {
    Event::fake();

    $tenantModel = $this->tenantModel;
    $userModel = $this->userModel;

    $team = $tenantModel::factory()->create();
    $author = $userModel::factory()->create();
    $reporter = $userModel::factory()->create();

    actingAs($reporter);
    Filament::setTenant($team);

    $tenantColumn = Forum::getTenantColumnName();
    $forum = Forum::factory()->create([$tenantColumn => $team->id]);
    $post = ForumPost::factory()->create([
        'forum_id' => $forum->id,
        'user_id' => $author->id,
        $tenantColumn => $team->id,
    ]);

    $report = $post->reportContent($reporter, 'spam', 'This is promotional.');

    expect($report)->toBeInstanceOf(ForumContentReport::class);
    expect($report->reportable->is($post))->toBeTrue();
    expect($report->reporter->is($reporter))->toBeTrue();
    expect($report->reason)->toBe('spam');
    expect($report->{$tenantColumn})->toBe($team->id);
    expect($post->hasPendingReportBy($reporter))->toBeTrue();
    expect($post->pending_reports_count)->toBe(1);
    expect($reporter->forumContentReports()->count())->toBe(1);

    Event::assertDispatched(ForumContentReported::class, fn (ForumContentReported $event): bool => $event->report->is($report));
});

it('updates an existing pending report by the same reporter', function () {
    $tenantModel = $this->tenantModel;
    $userModel = $this->userModel;

    $team = $tenantModel::factory()->create();
    $author = $userModel::factory()->create();
    $reporter = $userModel::factory()->create();

    actingAs($reporter);
    Filament::setTenant($team);

    $tenantColumn = Forum::getTenantColumnName();
    $forum = Forum::factory()->create([$tenantColumn => $team->id]);
    $post = ForumPost::factory()->create([
        'forum_id' => $forum->id,
        'user_id' => $author->id,
        $tenantColumn => $team->id,
    ]);

    $firstReport = $post->reportContent($reporter, 'spam', 'First details.');
    $secondReport = $post->reportContent($reporter, 'harassment', 'Updated details.');

    expect($secondReport->id)->toBe($firstReport->id);
    expect($post->contentReports()->count())->toBe(1);
    expect($secondReport->fresh()->reason)->toBe('harassment');
    expect($secondReport->fresh()->details)->toBe('Updated details.');
});

it('can resolve and dismiss reports', function () {
    Event::fake();

    $tenantModel = $this->tenantModel;
    $userModel = $this->userModel;

    $team = $tenantModel::factory()->create();
    $author = $userModel::factory()->create();
    $reporter = $userModel::factory()->create();
    $reviewer = $userModel::factory()->create();

    actingAs($reviewer);
    Filament::setTenant($team);

    $tenantColumn = Forum::getTenantColumnName();
    $forum = Forum::factory()->create([$tenantColumn => $team->id]);
    $post = ForumPost::factory()->create([
        'forum_id' => $forum->id,
        'user_id' => $author->id,
        $tenantColumn => $team->id,
    ]);

    $resolvedReport = $post->reportContent($reporter, 'spam');
    $resolvedReport->resolve($reviewer, 'Removed content.');

    expect($resolvedReport->fresh()->status)->toBe(ForumContentReport::STATUS_RESOLVED);
    expect($resolvedReport->fresh()->reviewer->is($reviewer))->toBeTrue();
    expect($resolvedReport->fresh()->reviewed_at)->not->toBeNull();
    expect($resolvedReport->fresh()->resolution_note)->toBe('Removed content.');

    Event::assertDispatched(ForumContentReportResolved::class, fn (ForumContentReportResolved $event): bool => $event->report->is($resolvedReport));

    $dismissedReport = $post->reportContent($reporter, 'other');
    $dismissedReport->dismiss($reviewer, 'No issue found.');

    expect($dismissedReport->fresh()->status)->toBe(ForumContentReport::STATUS_DISMISSED);
    expect(ForumContentReport::pending()->count())->toBe(0);
    expect(ForumContentReport::resolved()->count())->toBe(1);
    expect(ForumContentReport::dismissed()->count())->toBe(1);

    Event::assertDispatched(ForumContentReportDismissed::class, fn (ForumContentReportDismissed $event): bool => $event->report->is($dismissedReport));
});
