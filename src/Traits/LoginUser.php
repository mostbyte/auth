<?php

namespace Mostbyte\Auth\Traits;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Mostbyte\Auth\Constants\CacheConstant;
use Mostbyte\Auth\Exceptions\InvalidTokenException;
use Mostbyte\Auth\Models\User;

trait LoginUser
{
    /**
     * Check is token valid
     *
     * @param string|null $token
     * @return bool
     */
    public function checkTokens(?string $token): bool
    {
        $saved_token = Cache::get($th);

        return $saved_token && $token === $saved_token;
    }

    protected function cacheKey(...$keys): string
    {
        return CacheConstant::AUTH_USER->withPrefix(...$keys);
    }

    protected function tokenCacheKey(...$keys): string
    {
        return CacheConstant::AUTH_TOKEN->withPrefix(...$keys);
    }

    /**
     * @param string|null $token
     * @return array
     * @throws InvalidTokenException
     * @throws ConnectionException
     */
    public function prepareAttributesForLogin(?string $token = null): array
    {
        if (blank($token)) {
            $this->forceStop();
        }

        if ($this->checkTokens($token) && $attributes = Cache::get($this->cacheKey())) {
            return $attributes;
        }

        $data = identity()->checkToken($token);

        $attributes = $data['user'];

        if (!isset($attributes['company']) || !isset($attributes['role'])) {
            $this->forceStop();
        }

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

        if ($diff - CacheConstant::ttl() > 0) {
            return CacheConstant::ttl();
        }

        return $diff;
    }

    /**
     * @return void
     * @throws InvalidTokenException
     */
    public function forceStop(): void
    {
        $this->clearCache();

        throw new InvalidTokenException();
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
        $user = app(User::class, compact('attributes'));

        Auth::login($user);

        /** @var User $user */
        $user = Auth::user();

        $token = Cache::get($this->tokenCacheKey());

        if (blank($token)) {
            throw new InvalidTokenException("Token is empty");
        }

        $user->setToken($token);
    }
}
