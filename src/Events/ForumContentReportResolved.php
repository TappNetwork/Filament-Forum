<?php

namespace Tapp\FilamentForum\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Tapp\FilamentForum\Models\ForumContentReport;

class ForumContentReportResolved
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public ForumContentReport $report,
    ) {}
}
