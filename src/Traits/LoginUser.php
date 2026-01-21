<?php

declare(strict_types=1);

namespace Mostbyte\Auth\Traits;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Mostbyte\Auth\Enums\CacheKey;
use Mostbyte\Auth\Exceptions\InvalidTokenException;
use Mostbyte\Auth\Models\User;

trait LoginUser
{
    /**
     * Check is token valid
     *
     * @param string $token
     * @return bool
     */
    public function checkTokens(string $token): bool
    {
        return $token === Cache::get($this->tokenCacheKey());
    }

    protected function cacheKey(...$keys): string
    {
        return CacheKey::AUTH_USER->withPrefix(...$keys);
    }

    protected function tokenCacheKey(...$keys): string
    {
        return CacheKey::AUTH_TOKEN->withPrefix(...$keys);
    }

    /**
     * @param string|null $token
     * @param string|null $args
     * @return array
     * @throws ConnectionException
     * @throws InvalidTokenException
     */
    public function prepareAttributesForLogin(?string $token = null, ?string $args = null): array
    {
        if (blank($token)) {
            $this->forceStop('Token is empty');
        }

        if ($this->checkTokens($token) && $attributes = Cache::get($this->cacheKey())) {
            return $attributes;
        }

        $data = identity()->checkToken($token, $args);

        $attributes = $data['user'];

        Cache::put(
            $this->cacheKey(),
            $attributes,
            $this->setTTL($data["tokenExpires"])
        );

        Cache::put(
            $this->tokenCacheKey(),
            $token,
            $this->setTTL($data["tokenExpires"])
        );

        return $attributes;
    }

    /**
     * @param string $timestamp
     * @return int
     */
    protected function setTTL(string $timestamp): int
    {
        $date = Carbon::createFromTimeString($timestamp);

        $diff = now()->diffInSeconds($date, true);

        if ($diff - CacheKey::ttl() > 0) {
            return CacheKey::ttl();
        }

        return (int)$diff;
    }

    /**
     * @param string $message
     * @return void
     * @throws InvalidTokenException
     */
    public function forceStop(string $message = ''): void
    {
        $this->clearCache();

        throw new InvalidTokenException($message);
    }

    /**
     * @return void
     */
    public function clearCache(): void
    {
        Cache::forget($this->tokenCacheKey());
        Cache::forget($this->cacheKey());
    }

    /**
     * @throws InvalidTokenException
     */
    public function login(array $attributes): void
    {
        $token = Cache::get($this->tokenCacheKey());

        if (blank($token)) {
            $this->forceStop("Token is empty");
        }

        Auth::login($this->getUser($attributes));

        /** @var User $user */
        $user = Auth::user();

        $user->setToken($token);
    }
}
