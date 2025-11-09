<?php

namespace Tapp\FilamentForum\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Tapp\FilamentForum\Models\Traits\BelongsToTenant;

/**
 * @property int $id
 * @property int $forum_comment_id
 * @property int $reactor_id
 * @property string $reactor_type
 * @property string $type
 * @property ForumComment $forumComment
 * @property Model $reactor
 */
class ForumCommentReaction extends Model
{
    use BelongsToTenant;
    use HasFactory;

    protected $fillable = [
        'forum_comment_id',
        'reactor_id',
        'reactor_type',
        'type',
    ];

    public function forumComment(): BelongsTo
    {
        return $this->belongsTo(ForumComment::class);
    }

    public function reactor(): MorphTo
    {
        return $this->morphTo();
    }
}
