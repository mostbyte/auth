# Mostbyte Auth

Authorization package for Laravel applications using Mostbyte Identity Service.

## Requirements

| Dependency | Version    |
|:-----------|:-----------|
| PHP        | >= 8.4     |
| Laravel    | 11.x, 12.x |

## Version Compatibility

| Laravel Version | Package Version |
|:----------------|:----------------|
| < 10.x          | 2.x             |
| 11.x, 12.x      | 3.x             |

## Installation

Install via [Composer](https://getcomposer.org):

```bash
composer require mostbyte/auth
```

Or add manually to `composer.json`:

```json
{
  "require": {
    "mostbyte/auth": "^3.0"
  }
}
```

Then run:

```bash
composer update
```

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --provider="Mostbyte\Auth\AuthServiceProvider"
```

### Environment Variables

| Variable | Description | Default |
|:---------|:------------|:--------|
| `IDENTITY_BASE_URL` | Identity service URL | `https://auth.mostbyte.uz` |
| `LOCAL_DEVELOPMENT` | Enable mock responses | `true` |

> **Warning**
> Set `LOCAL_DEVELOPMENT=false` in production. Otherwise, all HTTP requests to the identity service will return fake responses.

## Usage

### Basic Middleware Usage

```php
use Mostbyte\Auth\Middleware\IdentityAuth;

Route::middleware(IdentityAuth::class)->get('/foo', function () {
    return 'bar';
});
```

### With No-Domain Parameter

```php
use Mostbyte\Auth\Middleware\IdentityAuth;

Route::middleware(IdentityAuth::using('no-domain'))->get('/foo', function () {
    return 'bar';
});
```

### Register as Alias

In `bootstrap/app.php` (Laravel 11+):

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'identity' => \Mostbyte\Auth\Middleware\IdentityAuth::class,
    ]);
})
```

Then use in routes:

```php
Route::middleware('identity')->get('/foo', function () {
    return 'bar';
});
```

### Accessing Authenticated User

```php
// Get authenticated user
$user = auth()->user();

// Access relationships
$company = auth()->user()->company;
$role = auth()->user()->role;

// Get token
$token = auth()->user()->getToken();
```

## License

MIT
