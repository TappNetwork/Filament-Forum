<?php

namespace Tapp\FilamentForum\Tests;

use Filament\FilamentServiceProvider;
use Filament\Support\SupportServiceProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Tapp\FilamentForum\FilamentForumServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        // Check if the parent class has the $latestResponse property before calling setUp
        if (property_exists(get_parent_class($this), 'latestResponse')) {
            parent::setUp();
        } else {
            // For older versions of TestBench that don't have $latestResponse
            $this->setUpTheTestEnvironment();
        }

        // Load test migrations first (users, teams)
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');

        // Load plugin migrations after test migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    protected function getPackageProviders($app)
    {
        return [
            LivewireServiceProvider::class,
            FilamentServiceProvider::class,
            SupportServiceProvider::class,
            FilamentForumServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Set up auth configuration with test User model
        $app['config']->set('auth.providers.users.model', \Tapp\FilamentForum\Tests\Models\User::class);

        // Configure plugin to use test models
        $app['config']->set('filament-forum.user.model', \Tapp\FilamentForum\Tests\Models\User::class);
        $app['config']->set('filament-forum.tenancy.model', \Tapp\FilamentForum\Tests\Models\Team::class);
        $app['config']->set('filament-forum.tenancy.enabled', true);
    }
}
