## Mostbyte Auth

#### Mostbyte authorization system from identity service

## Installation

To get the latest version of `Mostbyte auth`, simply require the project using [Composer](https://getcomposer.org)

```bash
composer require mostbyte/auth
```

Instead, you may of course manually update your require block and run `composer update` if you so choose:

```json
{
  "require": {
    "mostbyte/auth": "^1.0"
  }
}
```

## Publishing config files

```bash
php artisan vendor:publish --provider="Mostbyte\Auth\AuthServiceProvider"
```

> Warning: In production, in `.env` you should specify `LOCAL_DEVELOPMENT=false`. Otherwise your all
> http requests will be handled by faker

## Using

### Using in routes

```php
use Mostbyte\Auth\Middleware\IdentityAuth;

Route::middleware(IdentityAuth::class)->get("foo", function () {
    return "bar";
});
```

or specify in `App\Http\Kernel.php`

```php
protected $routeMiddleware = [
    // other middlewares...
    "identity" => \Mostbyte\Auth\Middleware\IdentityAuth::class
];
```

and in routes

```php
Route::middleware('identity')->get("foo", function () {
    return "bar";
});
```