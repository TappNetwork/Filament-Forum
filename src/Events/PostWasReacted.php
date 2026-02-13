<?php

namespace Tapp\FilamentForum\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
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
        public Model|Authenticatable $reactor,
        public ForumPost $post,
        public ForumPostReaction $reaction,
    ) {}
}
