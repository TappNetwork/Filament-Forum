<?php

declare(strict_types=1);

namespace Tapp\FilamentForum\Models;

use Database\Factories\ForumFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Forum extends Model implements HasMedia
{
    /** @use HasFactory<ForumFactory> */
    use HasFactory;

    use InteractsWithMedia;

    protected $guarded = [];

    protected $casts = [
        'is_hidden' => 'boolean',
    ];

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

    /**
     * Get all users assigned to this forum (via forum_user).
     */
    public function users(): BelongsToMany
    {
        $userModel = config('auth.providers.users.model', Authenticatable::class);
        
        return $this->belongsToMany($userModel, 'forum_user', 'forum_id', 'user_id')->withTimestamps();
    }

    /**
     * Scope a query to only include forums accessible to a specific user.
     */
    public function scopeAccessibleTo(Builder $query, $user): void
    {
        $isForumAdmin = method_exists($user, 'isForumAdmin') && $user->isForumAdmin();
        
        $query->where(function ($q) use ($user, $isForumAdmin) {
            // Public forums (is_hidden = false) - accessible to everyone
            $q->where('is_hidden', false);
            
            // Hidden forums (is_hidden = true) - accessible to assigned users or forum admins
            if ($isForumAdmin) {
                // Forum admins can see all hidden forums
                $q->orWhere('is_hidden', true);
            } else {
                // Regular users can only see hidden forums they're assigned to
                $q->orWhere(function ($subQ) use ($user) {
                    $subQ->where('is_hidden', true)
                         ->whereHas('users', function ($userQuery) use ($user) {
                             $userQuery->where('user_id', $user->id);
                         });
                });
            }
        });
    }

    /**
     * Check if a user can access this forum.
     */
    public function canBeAccessedBy($user): bool
    {
        if (!$user) {
            return false;
        }

        // Public forums (is_hidden = false) are accessible to everyone
        if (!$this->is_hidden) {
            return true;
        }

        // Hidden forums (is_hidden = true) are accessible to assigned users or forum admins
        if (method_exists($user, 'isForumAdmin') && $user->isForumAdmin()) {
            return true;
        }

        return $this->users()->where('user_id', $user->id)->exists();
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
