<?php

namespace Tapp\FilamentForum\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Tapp\FilamentForum\Models\Traits\BelongsToTenant;

/**
 * @property int $id
 * @property int $forum_post_id
 * @property int $reactor_id
 * @property string $reactor_type
 * @property string $type
 * @property ForumPost $forumPost
 * @property Model $reactor
 */
class ForumPostReaction extends Model
{
    use BelongsToTenant;
    use HasFactory;

    protected $fillable = [
        'forum_post_id',
        'reactor_id',
        'reactor_type',
        'type',
    ];

    public function forumPost(): BelongsTo
    {
        return $this->belongsTo(ForumPost::class);
    }

    public function reactor(): MorphTo
    {
        return $this->morphTo();
    }
}
