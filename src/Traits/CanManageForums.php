<?php

namespace Tapp\FilamentForum\Traits;

trait CanManageForums
{
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
