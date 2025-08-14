# Filament Forum

A forum package for Filament apps that provides both admin and frontend resources for managing forums and forum posts.

## Requirements

- PHP 8.1+
- Laravel 10+
- Filament 4.0+

## Installation

1. **Require the package via Composer:**

   ```bash
   composer require tapp/filament-forum
   ```

2. **Publish the config file:**

   ```bash
   php artisan vendor:publish --tag="filament-forum-config"
   ```

3. **Register the Plugins:**

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

4. Add `HasFavoriteForumPost` trait to your User model:

```php
use Tapp\FilamentForum\Models\Traits\HasFavoriteForumPost;

class User extends Authenticatable
{
    // ...
    use HasFavoriteForumPost;
    // ...
}
```

That's it! The plugins will auto-register with Filament and be ready to use.

## Features

- **Admin Resources**: Full CRUD operations for forums and forum posts
- **Frontend Resources**: User-friendly interface for browsing and participating in forums

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
