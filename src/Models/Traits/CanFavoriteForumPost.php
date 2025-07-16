<?php

namespace Tapp\FilamentForum\Models\Traits;

use Tapp\FilamentForum\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait CanFavoriteForumPost
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
} 