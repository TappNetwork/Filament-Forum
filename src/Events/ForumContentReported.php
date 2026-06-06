<?php

namespace Tapp\FilamentForum\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Tapp\FilamentForum\Models\ForumContentReport;

class ForumContentReported
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public ForumContentReport $report,
    ) {}
}
