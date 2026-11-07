## Mostbyte Auth

#### Mostbyte authorization system from identity service

## Versions

| Laravel   | Auth |
|:----------|:-----|
| < 10.x    | 2.x  |
| 11.x,12.x | 3.x  |

## Installation

To get the latest version of `Mostbyte auth`, simply require the project using [Composer](https://getcomposer.org)

```bash
composer require mostbyte/auth
```

Instead, you may of course manually update your require block and run `composer update` if you so choose:

```json
{
  "require": {
    "mostbyte/auth": "^3.0"
  }
}
```

## Publishing config files

```bash
php artisan vendor:publish --provider="Mostbyte\Auth\AuthServiceProvider"
```

> **Warning**
> `LOCAL_DEVELOPMENT=true` makes every request to the identity service return a fake
> success response, so **any token is accepted as a valid superUser**. The default is
> `false` (since 3.3.3): a service that never sets the variable is secure. Opt in
> explicitly with `LOCAL_DEVELOPMENT=true` in your local `.env` only.

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
protected $middlewareAliases = [
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
