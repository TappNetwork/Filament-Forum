<?php

namespace Tapp\FilamentForum\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Tapp\FilamentForum\Models\Forum;

class ForumSubscribed
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public mixed $user,
        public Forum $forum,
    ) {}
}
