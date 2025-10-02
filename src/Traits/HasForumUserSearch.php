<?php

namespace Tapp\FilamentForum\Traits;

trait HasForumUserSearch
{
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
}
