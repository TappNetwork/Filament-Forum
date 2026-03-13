<?php

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tapp\FilamentForum\Tests\TestCase;

uses(
    TestCase::class,
    LazilyRefreshDatabase::class,
)->in(__DIR__);

// Pest helper functions
function actingAs($user, $guard = null)
{
    return test()->actingAs($user, $guard);
}
