<?php

namespace Tapp\FilamentForum\Models\Traits;

use Filament\Facades\Filament;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait CanFavoriteForumPost
{
    public function toggleFavorite(): void
    {
        $pivotData = [];

        // Add tenant_id if tenancy is enabled
        if (config('filament-forum.tenancy.enabled') && Filament::hasTenancy()) {
            $tenantColumn = config('filament-forum.tenancy.column') ?: 'team_id';
            $pivotData[$tenantColumn] = Filament::getTenant()->id;
        }

        auth()->user()->favoriteForumPosts()->toggle([$this->id => $pivotData]);
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
        $relationship = $this->belongsToMany(config('auth.providers.users.model'), 'favorite_forum_post');

        // Add tenant column to pivot if tenancy is enabled
        if (config('filament-forum.tenancy.enabled')) {
            $tenantColumn = config('filament-forum.tenancy.column') ?: 'team_id';
            $relationship->withPivot($tenantColumn);
        }

        return $relationship;
    }

    public function scopeFavorited($query): Builder
    {
        return $query->whereHas('favoritedUsers', function ($query) {
            $query->where('favorite_forum_post.user_id', auth()->id());

            // Add tenant scoping if tenancy is enabled
            if (config('filament-forum.tenancy.enabled') && Filament::hasTenancy()) {
                $tenantColumn = config('filament-forum.tenancy.column') ?: 'team_id';
                $query->where('favorite_forum_post.'.$tenantColumn, Filament::getTenant()->id);
            }
        });
    }

    public function scopeNotFavorited($query): Builder
    {
        return $query->whereDoesntHave('favoritedUsers', function ($query) {
            $query->where('favorite_forum_post.user_id', auth()->id());

            // Add tenant scoping if tenancy is enabled
            if (config('filament-forum.tenancy.enabled') && Filament::hasTenancy()) {
                $tenantColumn = config('filament-forum.tenancy.column') ?: 'team_id';
                $query->where('favorite_forum_post.'.$tenantColumn, Filament::getTenant()->id);
            }
        });
    }
}
