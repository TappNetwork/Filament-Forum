<?php

namespace Tapp\FilamentForum\Models\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Tapp\FilamentForum\Models\ForumPost;

trait HasFavoriteForumPost
{
    public function favoriteForumPosts(): BelongsToMany
    {
        $relationship = $this->belongsToMany(ForumPost::class, 'favorite_forum_post');

        // Add tenant column to pivot if tenancy is enabled
        if (config('filament-forum.tenancy.enabled')) {
            $tenantColumn = config('filament-forum.tenancy.column') ?: 'team_id';
            $relationship->withPivot($tenantColumn);
        }

        return $relationship;
    }
}
