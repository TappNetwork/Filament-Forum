<?php

declare(strict_types=1);

namespace Tapp\FilamentForum\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Tapp\FilamentForum\Filament\Resources\ForumPosts\ForumPostResource;
use Tapp\FilamentForum\Filament\Resources\Forums\ForumResource;

class ForumPlugin implements Plugin
{
    public function getId(): string
    {
        return 'forum';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            ForumPostResource::class,
            ForumResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return new static;
    }

    public static function get(): static
    {
        return filament(app(static::class)->getId());
    }
}
