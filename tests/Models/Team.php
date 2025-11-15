<?php

namespace Tapp\FilamentForum\Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Get the factory for the model.
     */
    protected static function newFactory()
    {
        return \Tapp\FilamentForum\Tests\Database\Factories\TeamFactory::new();
    }
}
