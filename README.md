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

- Add `HasFavoriteForumPost` trait to your `User` model:

```php
use Tapp\FilamentForum\Models\Traits\HasFavoriteForumPost;

class User extends Authenticatable
{
    // ...
    use HasFavoriteForumPost;
    // ...
}
```

- Add `HasMentionables` trait (you can use it to customize which users are mentionable, see below in "Custom Mentionables") to your `User` model

```php
use Tapp\FilamentForum\Models\Traits\HasMentionables;

class User extends Authenticatable
{
    use HasMentionables;
}
```

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

By default, the `name` column of `User` model is used for the `user` relationship. You can customize it using the  `title-attribute` on `filament-form.php` config file.

The Filament Forum plugin also supports custom search functionality for user selects in forms and filters. This allows you to customize which users' columns are used to search and display in dropdowns (eg. if your `User` model doesn't have a `name` column).

## Setup

### 1. Add the Trait to Your User Model

Add the `HasForumUserSearch` trait to your User model:

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tapp\FilamentForum\Traits\HasForumUserSearch;

class User extends Authenticatable
{
    use HasForumUserSearch;
    
    // Your existing model code...
}
```

### 2. Configure Your User Model Class

Make sure your `config/filament-forum.php` file points to the correct `User` model:

```php
'user' => [
    'title-attribute' => 'name',
    'model' => 'App\\Models\\User',  // Your User model class
],
```

### 3. Customize the Search Methods

Override the trait methods in your User model to customize search behavior:

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tapp\FilamentForum\Traits\HasForumUserSearch;

class User extends Authenticatable
{
    use HasForumUserSearch;
    
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

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

If you discover any security-related issues, please email security@tappnetwork.com.

## Credits

-  [Tapp Network](https://github.com/TappNetwork)
-  [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
