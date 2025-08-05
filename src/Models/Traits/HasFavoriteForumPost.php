<?php

namespace Tapp\FilamentForum\Models\Traits;

use Tapp\FilamentForum\Models\ForumPost;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasFavoriteForumPost
{
    public function favoriteForumPosts(): BelongsToMany
    {
        return $this->belongsToMany(ForumPost::class, 'favorite_forum_post');
    }
}
