<?php

namespace Tapp\FilamentForum\Models\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Tapp\FilamentForum\Models\ForumPost;

trait HasFavoriteForumPost
{
    public function favoriteForumPosts(): BelongsToMany
    {
        return $this->belongsToMany(ForumPost::class, 'favorite_forum_post');
    }
}
