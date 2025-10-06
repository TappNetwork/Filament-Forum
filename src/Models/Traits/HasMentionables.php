<?php

namespace Tapp\FilamentForum\Models\Traits;

trait HasMentionables
{
    public static function getMentionableUsers()
    {
        return static::all();
    }
}
