# Filament Forum

A forum package for Filament apps that provides both admin and frontend resources for managing forums and forum posts.

## Requirements

- PHP 8.1+
- Laravel 10+
- Filament 4.x/5.x
- Spatie Laravel Media Library

## Features

- **Admin Resources**: Full CRUD operations for forums and forum posts
- **Frontend Resources**: User-friendly interface for browsing and participating in forums

## Installation

1. **Require the package via Composer:**

   ```bash
   composer require tapp/filament-forum
   ```

2. **Publish and run the migrations:**

> [!WARNING]  
> If you are using multi-tenancy please see the "Multi-Tenancy Support" instructions below **before** publishing and running migrations.

   ```bash
   php artisan vendor:publish --tag="filament-forum-migrations"
   php artisan migrate
   ```

3. **Publish the config file:**

   ```bash
   php artisan vendor:publish --tag="filament-forum-config"
   ```

4. **Register the Plugins:**

   ### Admin Panel (Backend)
   
   Add the admin plugin to your `AdminPanelProvider.php`:

   ```php
   use Tapp\FilamentForum\Filament\ForumAdminPlugin;

   public function panel(Panel $panel): Panel
   {
       return $panel
           // ... other configuration
           ->plugins([
               ForumAdminPlugin::make(),
               // ... other plugins
           ]);
   }
   ```

   ### Frontend Panel (User-facing)
   
   Add the frontend plugin to your `AppPanelProvider.php` (or any frontend panel):

   ```php
   use Tapp\FilamentForum\Filament\ForumPlugin;

   public function panel(Panel $panel): Panel
   {
       return $panel
           // ... other configuration
           ->plugins([
               ForumPlugin::make(),
               // ... other plugins
           ]);
   }
   ```

5. User model requirements

- Ensure your User model has a `name` attribute

- Add the `ForumUser` trait to your `User` model:

```php
use Tapp\FilamentForum\Traits\ForumUser;

class User extends Authenticatable
{
    // ...
    use ForumUser;
    // ...
}
```

The `ForumUser` trait provides:
- `forums()` relationship - Get all forums the user is assigned to
- `favoriteForumPosts()` relationship - Get all favorite forum posts
- `getMentionableUsers()` method - Customize which users are mentionable (see "Custom Mentionables" below)
- `hasCustomForumSearch()`, `getForumSearchResults()`, `getForumOptionLabel()` methods - Custom search functionality (see "Custom User Model, Attribute, and Search Functionality" below)
- `isForumAdmin()` method - Override to grant admin access to hidden forums (defaults to `false`)

6. Add to your custom theme (usually`theme.css`) file:

To include the TailwindCSS styles used on frontend pages, add to your theme file:

```css
@source '../../../../vendor/tapp/filament-forum';
```

That's it! The plugins will auto-register with Filament and be ready to use.

Optionally, you can publish the translation files with:

```bash
php artisan vendor:publish --tag="filament-forum-translations"
```

## Multi-Tenancy Support

Filament Forum includes built-in support for multi-tenancy, allowing you to scope forums and forum posts to specific tenants (e.g., teams, organizations, workspaces).

### ⚠️ Important: Enable Tenancy Before Migrations

**You MUST configure and enable tenancy in the config file BEFORE running the migrations.** The migrations check the tenancy configuration to determine whether to add tenant columns to the database tables. If you enable tenancy after running migrations, you'll need to manually add the tenant columns to your database.

### Setup

#### 1. Configure Tenancy (Before Migrations!)

Edit your `config/filament-forum.php` file **before** running the migrations:

```php
'tenancy' => [
    // Enable tenancy support
    'enabled' => true,

    // The Tenant model class (e.g., App\Models\Team::class, App\Models\Organization::class)
    'model' => \App\Models\Team::class,

    // The tenant relationship name (optional)
    // Defaults to snake_case of tenant model class name
    // For example: Team::class -> 'team', Organization::class -> 'organization'
    'relationship_name' => 'team',

    // The tenant column name (optional)
    // Defaults to snake_case of tenant model class name + '_id'
    // For example: Team::class -> 'team_id', Organization::class -> 'organization_id'
    'column' => 'team_id',
],
```

#### 2. Run Migrations

Now you can safely run the migrations, which will include the tenant columns:

```bash
php artisan vendor:publish --tag="filament-forum-migrations"
php artisan migrate
```

The following tables will include your tenant column (e.g., `team_id`):
- `forums`
- `forum_posts`
- `forum_comments`
- `forum_comment_reactions`
- `forum_post_views`

#### 3. Configure Your Filament Panel

Make sure tenancy is enabled on your Filament panel (e.g., in `AdminPanelProvider.php`):

```php
use Filament\Panel;
use App\Models\Team;

public function panel(Panel $panel): Panel
{
    return $panel
        // ... other configuration
        ->tenant(Team::class)
        ->plugins([
            ForumAdminPlugin::make(),
            // ... other plugins
        ]);
}
```

#### 4. Implement Required Contracts on Your User Model

Your User model needs to implement Filament's tenancy contracts (using `teams` tenant in this example):

```php
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

class User extends Authenticatable implements FilamentUser, HasTenants
{
    // Define the relationship to your tenant model (eg. teams)
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class);
    }

    // Required by FilamentUser
    public function canAccessPanel(Panel $panel): bool
    {
        return true; // Customize as needed
    }

    // Required by HasTenants
    public function getTenants(Panel $panel): Collection
    {
        return $this->teams;
    }

    // Required by HasTenants
    public function canAccessTenant(Model $tenant): bool
    {
        return $this->teams()->whereKey($tenant)->exists();
    }
}
```

#### 5. Implement Required Contracts on Your Tenant Model

Your Tenant model (e.g., `Team`) should implement Filament's `HasName` contract:

```php
use Filament\Models\Contracts\HasName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Team extends Model implements HasName
{
    // Define the inverse relationship
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    // Required by HasName
    public function getFilamentName(): string
    {
        return $this->name;
    }
}
```

### How It Works

Once tenancy is enabled:

1. **Automatic Scoping**: All forum and forum post queries are automatically scoped to the current tenant
2. **Automatic Association**: When creating forums or posts, they're automatically associated with the current tenant
3. **Access Control**: Users can only access forums and posts belonging to their current tenant

### URL Structure with Tenancy

With tenancy enabled, your Filament panel URLs will include the tenant identifier:

```
/admin/{tenant-slug}/forums
/admin/{tenant-slug}/forum-posts
```

For example:
```
/admin/acme-company/forums
/admin/acme-company/forum-posts
```

### Disabling Tenancy

To disable tenancy, simply set `'enabled' => false` in your `config/filament-forum.php`:

```php
'tenancy' => [
    'enabled' => false,
    'model' => null,
],
```

**Note**: If you've already run migrations with tenancy enabled, the tenant columns will remain in your database. You'll need to handle existing tenant-scoped data appropriately or create a migration to remove the columns if needed.

### Important Notes

- **Migrations First**: Always configure tenancy before running migrations
- **Data Migration**: If you enable tenancy on an existing installation, you'll need to manually add tenant columns and populate them with appropriate values
- **Testing**: When testing with tenancy enabled, ensure your factories and seeders properly associate records with tenants

For more detailed information about implementing multi-tenancy in Filament, see the [official Filament tenancy documentation](https://filamentphp.com/docs/4.x/users/tenancy).

## Custom User Model, Attribute, and Search Functionality

By default, the `name` column of `User` model is used for the `user` relationship. You can customize it using the  `title-attribute` on `filament-forum.php` config file.

The Filament Forum plugin also supports custom search functionality for user selects in forms and filters. This allows you to customize which users' columns are used to search and display in dropdowns (eg. if your `User` model doesn't have a `name` column).

### Configure Your User Model Class

Make sure your `config/filament-forum.php` file points to the correct `User` model:

```php
'user' => [
    'title-attribute' => 'name',
    'model' => 'App\\Models\\User',  // Your User model class
],
```

### Customize the Search Methods

Override the trait methods in your User model to customize search behavior:

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tapp\FilamentForum\Traits\ForumUser;

class User extends Authenticatable
{
    use ForumUser;
    
    /**
     * Custom search that searches both name and email
     */
    public static function getForumSearchResults(string $search): array
    {
        return static::query()
            ->where('name', 'like', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%")
            ->limit(50)
            ->pluck('name', 'id')
            ->all();
    }
    
    /**
     * Custom option label display
     */
    public static function getForumOptionLabel($value): ?string
    {
        $user = static::find($value);
        
        return $user ? "{$user->name} ({$user->email})" : null;
    }
}
```

## Custom Mentionables

You can customize which users are mentionable by overriding the `getMentionableUsers()` method in your `User` model:

```php
// In your User model
public static function getMentionableUsers()
{
    // Only active users
    return static::where('is_active', true)->get();
}
```

## Forum Access Control

Forums can be set as public (visible to all logged-in users) or hidden (only visible to assigned users). Forum admins can see all hidden forums regardless of assignment.

### Setting Forum Access

In the admin panel, you can set a forum as hidden by checking the "Hidden Forum" checkbox. Hidden forums will only be visible to:
- Users who are explicitly assigned to the forum
- Forum admins (users where `isForumAdmin()` returns `true`)

### Forum Admin Access

Override the `isForumAdmin()` method in your User model to grant admin access:

```php
// In your User model
public function isForumAdmin(): bool
{
    // Example: Grant admin access based on roles
    return $this->hasRole('Admin') || $this->hasRole('Forum Admin');
}
```

Forum admins can see and access all hidden forums, even if they're not assigned to them.

## User Avatar

Optionally, implements Filament's `HasAvatar` interface:

```php
use Filament\Models\Contracts\HasAvatar;

class User extends Authenticatable implements HasAvatar
{   
    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url;
    }
}
```

## Events Dispatched

The plugin automatically dispatches events when a forum is created, a comment is created, reacted to, or when users are mentioned in a comment:

- `Tapp\FilamentForum\Events\ForumCommentCreated`
- `Tapp\FilamentForum\Events\ForumPostCreated`
- `Tapp\FilamentForum\Events\CommentWasReacted`
- `Tapp\FilamentForum\Events\UserWasMentioned`

### UserWasMentioned Event

You can send a notification by listening to the `UserWasMentioned` event. The event structure is:

```php
use Tapp\FilamentForum\Events\UserWasMentioned;

// The event contains:
// - $mentionedUser: The user who was mentioned
// - $comment: The ForumComment instance
```

Example usage:

```php
namespace App\Listeners;

use App\Notifications\UserMentionedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Tapp\FilamentForum\Events\UserWasMentioned;

class SendUserMentionedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(UserWasMentioned $event): void
    {
        $mentionedUser = $event->mentionedUser;
        $comment = $event->comment;
        
        // Send notification to the mentioned user
        $mentionedUser->notify(new UserMentionedNotification($comment));
        
        // Or dispatch a custom notification
        Notification::make()
            ->title('You were mentioned in a comment')
            ->body("You were mentioned by {$comment->author->name}")
            ->sendToDatabase($mentionedUser);
    }
}
```

This should work with [Laravel's event auto-discovery](https://laravel.com/docs/11.x/events#registering-events-and-listeners). If not, you can register your listener on `EventServiceProvider`:

```php
use Tapp\FilamentForum\Events\UserWasMentioned;
use App\Listeners\SendUserMentionedNotification;

protected $listen = [
    UserWasMentioned::class => [
        SendUserMentionedNotification::class,
    ],
];
```

Custom notification example:

```php
namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Tapp\FilamentForum\Models\ForumComment;

class UserMentionedNotification extends Notification
{
    public function __construct(
        public ForumComment $comment
    ) {}

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'You were mentioned',
            'body' => "You were mentioned in a comment by {$this->comment->author->name}",
            'comment_id' => $this->comment->id,
            'forum_post_id' => $this->comment->forumPost->id,
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('You were mentioned in a forum comment')
            ->line("You were mentioned in a comment by {$this->comment->author->name}")
            ->action('View Comment', route('forum.posts.show', $this->comment->forumPost));
    }
}
```

## Testing

### Publishing Tests to Your Application

You can publish ready-to-use test files to your application to test the Filament Forum functionality:

```bash
php artisan filament-forum:install-tests
```

This will copy test files to your `tests/Feature` directory:
- `FilamentForumTest.php` - Basic forum and post functionality tests
- `FilamentForumTenancyTest.php` - Multi-tenancy specific tests (automatically skipped if tenancy is disabled)

The tests are written using [Pest](https://pestphp.com/) and automatically use your configured User and Tenant models from `config/filament-forum.php`.

**Requirements:**
- Pest testing framework: `composer require pestphp/pest --dev`
- Model factories for your User model and Tenant model (if using tenancy)

**Run the tests:**
```bash
php artisan test --filter=FilamentForum
```

See the published tests for more examples of how to test the plugin in your application.

Read more about publishing tests [here](PUBLISH_TESTS.md).

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

If you discover any security-related issues, please email security@tappnetwork.com.

## Credits

-  [Tapp Network](https://github.com/TappNetwork)
-  Comments inspired by [Commentions](https://github.com/kirschbaum-development/commentions)
-  [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
