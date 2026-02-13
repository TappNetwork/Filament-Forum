<?php

namespace Tapp\FilamentForum\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Tapp\FilamentForum\Models\ForumPost;
use Tapp\FilamentForum\Models\ForumPostReaction;

class PostWasReacted
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public $reactor,
        public ForumPost $post,
        public ForumPostReaction $reaction,
    ) {}
}
