<?php

namespace Tapp\FilamentForum\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Tapp\FilamentForum\Models\Forum;
use Tapp\FilamentForum\Models\ForumPost;

trait ForumUser
{
    /**
     * Get all forums the user is assigned to (via forum_user).
     */
    public function forums(): BelongsToMany
    {
        return $this->belongsToMany(Forum::class, 'forum_user', 'user_id', 'forum_id')->withTimestamps();
    }

    /**
     * Get all favorite forum posts.
     */
    public function favoriteForumPosts(): BelongsToMany
    {
        return $this->belongsToMany(ForumPost::class, 'favorite_forum_post');
    }

    /**
     * Get mentionable users for forum mentions.
     */
    public static function getMentionableUsers()
    {
        return static::all();
    }

    /**
     * Check if custom forum search methods are actually implemented.
     * Returns true if the user has overridden the search methods with actual logic.
     */
    public static function hasCustomForumSearch(): bool
    {
        // Test if getForumSearchResults returns actual results (not null)
        $testResult = static::getForumSearchResults('test');

        return $testResult !== null;
    }

    /**
     * Get search results for forum user selects.
     * Override this method in your User model to customize search behavior.
     *
     * @param  string  $search  The search term
     * @return array Array of options in format ['id' => 'label']
     */
    public static function getForumSearchResults(string $search): ?array
    {
        // Example:
        // return static::query()
        //     ->where('name', 'like', "%{$search}%")
        //     ->limit(50)
        //     ->pluck('name', 'id')
        //     ->all();

        return null;
    }

    /**
     * Get option label for a specific user ID in forum selects.
     * Override this method in your User model to customize label display.
     *
     * @param  mixed  $value  The user ID
     * @return string|null The label for the option, or null if not found
     */
    public static function getForumOptionLabel($value): ?string
    {
        // Example:
        // $user = static::find($value);

        // return $user?->name;

        return null;
    }

    /**
     * Determine if the user is a forum admin.
     * Forum admins can see hidden forums even if they are not assigned to them.
     * Override this method in your User model to customize admin logic.
     */
    public function isForumAdmin(): bool
    {
        // Default implementation: not an admin
        // Override this in your User model to implement admin logic
        return false;
    }

    /**
     * Determine if the user can create forums.
     *
     * By default, this returns false for security. Override this method in your
     * User model to enable forum creation with custom logic.
     *
     * Example:
     * ```
     * public function canCreateForum(): bool
     * {
     *     return $this->hasPermissionTo('create forum');
     * }
     * ```
     */
    public function canCreateForum(): bool
    {
        return false;
    }
}
