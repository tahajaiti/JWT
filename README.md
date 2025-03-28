# Kyojin JWT Authentication Package

## Overview

A robust and easy-to-use JWT (JSON Web Token) authentication package for Laravel, providing seamless token-based authentication with minimal configuration.

## Features

- Simple JWT token generation
- Middleware-based route protection
- Configurable token settings
- Easy integration with Laravel applications
- Supports custom user models

## Requirements

- PHP 8.2+
- Laravel 12.0
- Firebase JWT Library

## Installation

Install the package via Composer:

```bash
composer require kyojin/jwt
```

### Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag=jwt-config
```

This will create a `config/jwt.php` file with default settings.

## Environment Configuration

The package automatically adds necessary environment variables during installation:

```env
JWT_SECRET=your-secret-key
JWT_ALGO=HS256
JWT_TTL=60
```

## Usage

### Token Generation

```php
use Kyojin\JWT\Facades\JWT;

// Generate token for a user
$token = JWT::generate($user);
```

### Middleware Protection

Protect routes using the JWT middleware:

```php
Route::middleware('jwt')->group(function () {
    Route::get('/profile', ProfileController::class);
    Route::post('/update', UpdateController::class);
});
```

### Manual Token Validation

```php
use Kyojin\JWT\Facades\JWT;

try {
    // Validate token
    JWT::validate($token);

    // Get authenticated user
    $user = JWT::user();
} catch (\Exception $e) {
    // Handle invalid token
    return response()->json(['error' => $e->getMessage()], 401);
}
```

## Configuration Options

Edit `config/jwt.php` to customize:

```php
return [
    'secret' => env('JWT_SECRET'),
    'algo' => 'HS256',
    'ttl' => 60, // Token lifetime in minutes
    'user_model' => App\Models\User::class,
];
```

## Middleware Usage in Controllers

```php
class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt');
    }

    public function index()
    {
        // Only authenticated users can access
        return auth()->user();
    }
}
```

## Security Considerations

- Keep `JWT_SECRET` confidential
- Use strong, unique secret keys
- Rotate secrets periodically
- Use HTTPS in production

## Error Handling

The package throws exceptions for:

- Invalid tokens
- Expired tokens
- Non-existent users

Catch and handle these in your application logic.

## Advanced Usage

### Custom User Model

Specify a custom user model in `config/jwt.php`:

```php
'user_model' => App\Models\CustomUser::class
```

## Troubleshooting

- Ensure `JWT_SECRET` is set in `.env`
- Verify user model configuration
- Check token generation and validation
- Run `php artisan config:clear` after configuration changes

## Contributing

Contributions are welcome! Please submit pull requests or open issues on the GitHub repository.

## License

MIT License

## Support

For issues, please open a GitHub issue or contact the package maintainer.

## Version

Current version: 1.0.4
