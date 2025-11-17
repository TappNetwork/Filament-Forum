# Test Integration Guide

This guide explains how the test stubs integrate with your Laravel application.

## How It Works

When you run `php artisan filament-forum:install-tests`, the command copies test files from the plugin's `stubs/tests` directory to your application's `tests/Feature` directory.

### Configuration Integration

The tests automatically read from your application's configuration:

```php
// In the test files
$userModel = config('filament-forum.user.model');
$tenantModel = config('filament-forum.tenancy.model');
```

This means the tests will use **your** User and Tenant models, not the plugin's internal test models.

## Test Structure

### FilamentForumTest.php

Tests basic forum functionality that works with or without tenancy:

```php
it('can create a forum', function () {
    $user = $this->userModel::factory()->create();
    $this->actingAs($user);
    
    $forum = Forum::factory()->create([
        'name' => 'Test Forum',
    ]);
    
    expect($forum->name)->toBe('Test Forum');
});
```

### FilamentForumTenancyTest.php

Tests multi-tenancy features and automatically skips if tenancy is disabled:

```php
beforeEach(function () {
    if (! config('filament-forum.tenancy.enabled')) {
        $this->markTestSkipped('Tenancy is not enabled');
    }
    // ...
});
```

## Dynamic Tenant Column Names

The tests use the plugin's helper methods to get the correct tenant relationship and column names:

```php
// Get the tenant relationship name (e.g., 'team', 'organization', 'company')
$tenantRelationship = Forum::getTenantRelationshipName();

// Get the tenant column name (e.g., 'team_id', 'organization_id', 'company_id')
$tenantColumn = Forum::getTenantRelationshipName() . '_id';
```

This ensures tests work regardless of what you name your tenant model.

## Example Scenarios

### Scenario 1: Using Team Model

```php
// config/filament-forum.php
'tenancy' => [
    'enabled' => true,
    'model' => App\Models\Team::class,
],
```

Tests will:
- Use `App\Models\Team::factory()` to create tenants
- Look for `team_id` column in tables
- Use `team()` relationship method

### Scenario 2: Using Organization Model

```php
// config/filament-forum.php
'tenancy' => [
    'enabled' => true,
    'model' => App\Models\Organization::class,
],
```

Tests will:
- Use `App\Models\Organization::factory()` to create tenants
- Look for `organization_id` column in tables
- Use `organization()` relationship method

### Scenario 3: No Tenancy

```php
// config/filament-forum.php
'tenancy' => [
    'enabled' => false,
],
```

Tests will:
- Run all basic tests from `FilamentForumTest.php`
- Skip all tests in `FilamentForumTenancyTest.php`

## Customizing Tests

The published tests are meant to be modified for your needs. Here are some common customizations:

### Adding Custom Fields

```php
it('can create a forum with custom fields', function () {
    $user = $this->userModel::factory()->create();
    $this->actingAs($user);
    
    $forum = Forum::factory()->create([
        'name' => 'Test Forum',
        'slug' => 'test-forum',
        'custom_field' => 'custom value', // Your custom field
    ]);
    
    expect($forum->custom_field)->toBe('custom value');
});
```

### Testing Custom Permissions

```php
it('respects forum permissions', function () {
    $user = $this->userModel::factory()->create();
    $admin = $this->userModel::factory()->create(['role' => 'admin']);
    
    $this->actingAs($user);
    
    $this->assertDatabaseMissing('forums', ['name' => 'Admin Forum']);
    
    $this->actingAs($admin);
    
    $forum = Forum::factory()->create(['name' => 'Admin Forum']);
    
    expect($forum)->toBeInstanceOf(Forum::class);
});
```

### Testing with Multiple Tenants

```php
it('can handle multiple tenant memberships', function () {
    $user = $this->userModel::factory()->create();
    $tenant1 = $this->tenantModel::factory()->create();
    $tenant2 = $this->tenantModel::factory()->create();
    
    // Add user to both tenants (implement based on your membership logic)
    $user->teams()->attach([$tenant1->id, $tenant2->id]);
    
    $this->actingAs($user);
    
    // Test tenant switching
    Filament::setTenant($tenant1);
    expect(Filament::getTenant()->id)->toBe($tenant1->id);
    
    Filament::setTenant($tenant2);
    expect(Filament::getTenant()->id)->toBe($tenant2->id);
});
```

## Best Practices

1. **Run tests after publishing**: Immediately after running `filament-forum:install-tests`, run the tests to verify they work with your setup
2. **Customize for your needs**: The stubs are a starting point - modify them to match your application's requirements
3. **Keep tests in sync**: When you upgrade the plugin, review the test stubs for any changes
4. **Use factories**: Ensure your User and Tenant models have proper factories for reliable testing
5. **Test edge cases**: Add tests for your specific business logic and edge cases

## Troubleshooting

### Tests fail with "Factory not found"

Create factories for your models:

```bash
php artisan make:factory UserFactory --model=User
php artisan make:factory TeamFactory --model=Team
```

### Tests fail with "Table not found"

Run migrations in your test environment:

```bash
php artisan migrate --env=testing
```

Or configure in-memory SQLite in `phpunit.xml`:

```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

Or add your test database in `phpunit.xml`:

```xml
<env name="APP_ENV" value="testing"/>
<env name="DB_DATABASE" value="myapp_test"/>
```

### Tenancy tests always skip

Verify configuration:

```bash
php artisan config:clear
php artisan tinker
>>> config('filament-forum.tenancy.enabled')
=> true
```
