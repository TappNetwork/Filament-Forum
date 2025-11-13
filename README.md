# Filament Forum

A forum package for Filament apps that provides both admin and frontend resources for managing forums and forum posts.

## Requirements

- PHP 8.1+
- Laravel 10+
- Filament 4.0+

## Features

- **Admin Resources**: Full CRUD operations for forums and forum posts
- **Frontend Resources**: User-friendly interface for browsing and participating in forums

## Installation

1. **Require the package via Composer:**

   ```bash
   composer require tapp/filament-forum
   ```

2. **Publish and run the migrations:**

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
