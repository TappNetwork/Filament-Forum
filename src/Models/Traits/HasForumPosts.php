<?php

namespace Tapp\FilamentForum\Models\Traits;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;
use Tapp\FilamentForum\Models\ForumPost;

trait HasForumPosts
{
    public function toggleFavorite(): void
    {
        auth()->user()->favoriteForumPosts()->toggle($this->id);
    }

    public function isFavorite(): bool
    {
        return auth()->user()->favoriteForumPosts->contains($this->id);
    }

    public function getIsFavoriteAttribute(): bool
    {
        return auth()->user()->favoriteForumPosts->contains($this->id);
    }

    public function favoritedUsers(): BelongsToMany
    {
        return $this->belongsToMany(config('auth.providers.users.model'), 'favorite_forum_post');
    }

    public function favoriteForumPosts(): BelongsToMany
    {
        return $this->belongsToMany(ForumPost::class, 'favorite_forum_post');
    }

    public function scopeFavorited($query): Builder
    {
        return $query->whereHas('favoritedUsers', function ($query) {
            $query->where('user_id', auth()->id());
        });
    }

    public function scopeNotFavorited($query): Builder
    {
        return $query->whereDoesntHave('favoritedUsers', function ($query) {
            $query->where('user_id', auth()->id());
        });
    }

    /**
     * Check if the current user can edit this forum post.
     * By default, this blocks all editing. Override this method
     * in your project to implement custom authorization logic.
     */
    public function canEdit(?Model $user = null): bool
    {
        // By default, block all editing
        return false;
    }

    /**
     * Check if the current user can edit this forum post.
     * Uses the authenticated user if no user is provided.
     */
    public function canBeEditedBy(?Model $user = null): bool
    {
        $user = $user ?? Auth::user();
        
        if (!$user) {
            return false;
        }

        return $this->canEdit($user);
    }
}

