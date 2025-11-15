<?php

namespace Tapp\FilamentForum\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tapp\FilamentForum\Models\Traits\BelongsToTenant;

class ForumPostView extends Model
{
    use BelongsToTenant;

    protected $guarded = [];

    public function forumPost(): BelongsTo
    {
        return $this->belongsTo(ForumPost::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'user_id');
    }
}
