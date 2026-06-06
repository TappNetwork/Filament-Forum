<?php

namespace Tapp\FilamentForum\Models\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Tapp\FilamentForum\Models\Forum;
use Tapp\FilamentForum\Models\ForumPost;

trait HasForumSubscriptions
{
    public function subscribedForums(): BelongsToMany
    {
        $relationship = $this->belongsToMany(Forum::class, 'forum_subscriptions');

        if (config('filament-forum.tenancy.enabled')) {
            $relationship->withPivot(Forum::getTenantColumnName());
        }

        return $relationship->withTimestamps();
    }

    public function subscribedForumPosts(): BelongsToMany
    {
        $relationship = $this->belongsToMany(ForumPost::class, 'forum_post_subscriptions');

        if (config('filament-forum.tenancy.enabled')) {
            $relationship->withPivot(ForumPost::getTenantColumnName());
        }

        return $relationship->withTimestamps();
    }
}
