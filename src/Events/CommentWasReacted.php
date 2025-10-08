<?php

namespace Tapp\FilamentForum\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Tapp\FilamentForum\Models\ForumComment;
use Tapp\FilamentForum\Models\ForumCommentReaction;

class CommentWasReacted
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public $mentionedUser,
        public ForumComment $comment,
        public ForumCommentReaction $reaction,
    ) {}
}
