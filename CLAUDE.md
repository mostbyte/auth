# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Mostbyte Auth is a PHP Laravel authentication package that integrates with an external identity service API. It provides authentication middleware and user model system for Laravel applications.

- **Language:** PHP 8.4+
- **Framework:** Laravel 11.x / 12.x
- **Purpose:** Token-based authentication via external identity service

## Common Commands

```bash
# Run tests
vendor/bin/phpunit

# Run a single test
vendor/bin/phpunit --filter testMethodName

# Install dependencies
composer install

# Publish config to consuming Laravel app
php artisan vendor:publish --provider="Mostbyte\Auth\AuthServiceProvider"
```

## Architecture

### Authentication Flow

1. **Request** → HTTP request with `Authorization: Bearer <token>` header
2. **IdentityAuth Middleware** → Extracts token, validates via `LoginUser::prepareAttributesForLogin()`
3. **Identity Service Client** → Makes HTTP request to `IDENTITY_BASE_URL/api/v1/auth/check-token`
4. **Cache Layer** → Caches user/token with TTL (2 hours default), key includes company + IP + device-id
5. **User Model** → Created with company and role relationships, authenticated via Laravel Auth

### Key Components

| File | Purpose |
|------|---------|
| `src/Identity.php` | API client singleton for identity service requests |
| `src/Middleware/IdentityAuth.php` | Authentication middleware with optional `no-domain` parameter |
| `src/Traits/LoginUser.php` | Token validation, caching, and login logic |
| `src/Models/User.php` | Authenticatable user with UUID primary key, company/role relations |
| `src/Enums/CacheKey.php` | Cache key constants with TTL configuration |

### Middleware Usage

```php
// Standard usage
Route::middleware(IdentityAuth::class)->get("foo", fn() => "bar");

// With no-domain parameter (excludes domain from identity request)
Route::middleware(IdentityAuth::using('no-domain'))->get("foo", fn() => "bar");
```

### Environment Variables

- `IDENTITY_BASE_URL` - Identity service URL (default: `https://auth.mostbyte.uz`)
- `LOCAL_DEVELOPMENT` - Set `true` for mock responses. **Defaults to `false`** (since 5.0.0) because `true` accepts any token as superUser — opt in locally only, never in a deployed `.env`.

### Local Development Mode

When `LOCAL_DEVELOPMENT=true`, `AuthServiceProvider::registerFakeResponse()` installs an `Http::fake()` on `auth/check-token` that always returns `success: true` — so **every token is accepted as a valid superUser**. This is an authorization bypass, which is why the published default is `false`; a service that never sets the variable is secure by default. Opt in only in a local `.env`.

## Testing

Tests use Orchestra Testbench to simulate Laravel environment. Test files are in `tests/` directory with mocked HTTP responses to the identity service.

## Git

### Commits
- Do NOT add `Co-Authored-By` to commit messages

### Tags
- Do NOT use `v` prefix in version tags (use `4.1.0`, not `v4.1.0`)
