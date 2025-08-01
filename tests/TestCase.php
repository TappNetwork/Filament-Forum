<?php

namespace Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Tapp\FilamentForum\FilamentForumServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
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

        // Set up auth configuration
        $app['config']->set('auth.providers.users.model', \Illuminate\Foundation\Auth\User::class);

        // Run migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
