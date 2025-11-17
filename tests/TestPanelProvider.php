<?php

namespace Tapp\FilamentForum\Tests;

use Filament\Panel;
use Filament\PanelProvider;

class TestPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->default()
            ->tenant(\Tapp\FilamentForum\Tests\Models\Team::class);
    }
}
