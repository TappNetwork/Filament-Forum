<?php

use Tapp\FilamentForum\Tests\TestCase;

uses(
    TestCase::class,
    Illuminate\Foundation\Testing\RefreshDatabase::class,
)->in(__DIR__);

// Pest helper functions
function actingAs($user, $guard = null)
{
    return test()->actingAs($user, $guard);
}
