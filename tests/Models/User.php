<?php

namespace Tapp\FilamentForum\Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tapp\FilamentForum\Models\Traits\HasFavoriteForumPost;

class User extends Authenticatable
{
    use HasFactory;
    use HasFavoriteForumPost;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the factory for the model.
     */
    protected static function newFactory()
    {
        return \Tapp\FilamentForum\Tests\Database\Factories\UserFactory::new();
    }
}
