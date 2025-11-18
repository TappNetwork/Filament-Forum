<?php

namespace Tapp\FilamentForum\Tests;

use Filament\FilamentServiceProvider;
use Filament\Support\SupportServiceProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as Orchestra;
use Tapp\FilamentForum\FilamentForumServiceProvider;

abstract class TestCase extends Orchestra
{
    use WithWorkbench;

    protected function getPackageProviders($app)
    {
        return [
            LivewireServiceProvider::class,
            FilamentServiceProvider::class,
            SupportServiceProvider::class,
            FilamentForumServiceProvider::class,
            TestPanelProvider::class,
        ];
    }

    protected function defineEnvironment($app)
    {
        // Configure SQLite database
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Configure auth to use test User model
        $app['config']->set('auth.providers.users.model', \Tapp\FilamentForum\Tests\Models\User::class);

        // Configure plugin to use test models
        $app['config']->set('filament-forum.user.model', \Tapp\FilamentForum\Tests\Models\User::class);
        $app['config']->set('filament-forum.tenancy.model', \Tapp\FilamentForum\Tests\Models\Team::class);
        $app['config']->set('filament-forum.tenancy.enabled', true);
    }

    protected function defineDatabaseMigrations()
    {
        // Load test-specific migrations (users, teams)
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
    }
}
