<?php

declare(strict_types=1);

namespace Tapp\FilamentForum\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Tapp\FilamentForum\Database\Factories\ForumFactory;
use Tapp\FilamentForum\Models\Traits\BelongsToTenant;

class Forum extends Model implements HasMedia
{
    use BelongsToTenant;

    /** @use HasFactory<ForumFactory> */
    use HasFactory;

    use InteractsWithMedia;

    protected $guarded = [];

    protected static function newFactory()
    {
        return ForumFactory::new();
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'owner_id');
    }

    public function forumPosts(): HasMany
    {
        return $this->hasMany(ForumPost::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(ForumPost::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images');
    }

    public function getCountDistinctUsersWhoPosted()
    {
        return $this->forumPosts()->distinct('user_id')->count();
    }

    public function getLastActivity(): string
    {
        $lastActivity = $this->forumPosts()
            ->with('comments')
            ->latest()
            ->first()->created_at ?? $this->created_at;

        return $lastActivity?->diffForHumans() ?? '';
    }

    public function getRecentUsers(): Collection
    {
        return $this->forumPosts()
            ->with('user')
            ->select('user_id')
            ->selectRaw('MAX(created_at) as last_post_at')
            ->groupBy('user_id')
            ->orderByRaw('MAX(created_at) DESC')
            ->take(3)
            ->get()
            ->map(function ($post) {
                /** @phpstan-ignore-next-line */
                return $post->user;
            });
    }
}
