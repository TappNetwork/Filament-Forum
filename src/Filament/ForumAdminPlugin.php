<?php

declare(strict_types=1);

namespace Tapp\FilamentForum\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;

class ForumAdminPlugin implements Plugin
{
    public function getId(): string
    {
        return 'forum-admin';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources(
                config('filament-forum.admin-resources')
            );
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
