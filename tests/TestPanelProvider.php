<?php

namespace Tapp\FilamentForum\Tests;

use Filament\Panel;
use Filament\PanelProvider;
use Tapp\FilamentForum\Tests\Models\Team;

class TestPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->default()
            ->tenant(Team::class);
    }
}
