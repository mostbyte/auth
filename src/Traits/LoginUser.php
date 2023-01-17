<?php

namespace Mostbyte\Auth\Traits;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Mostbyte\Auth\Constants\CacheConstant;
use Mostbyte\Auth\Exceptions\InvalidTokenException;
use Mostbyte\Auth\Models\User;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

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
        return $token === Cache::get(CacheConstant::withPrefix(CacheConstant::AUTH_TOKEN));
    }

    /**
     * @param string|null $token
     * @return array
     * @throws InvalidTokenException
     * @throws RequestException
     */
    public function prepareAttributesForLogin(?string $token = null): array
    {
        if (!$token) {
            $this->forceStop();
        }

        if ($this->checkTokens($token) && $attributes = Cache::get(CacheConstant::withPrefix(CacheConstant::AUTH_USER))) {
            return $attributes;
        }

        $data = identity()->checkToken($token);

        if ($data['token'] !== $token) {
            $this->forceStop();
        }

        $attributes = $data['user'];

        if (!isset($attributes['company']) || !isset($attributes['role'])) {
            $this->forceStop();
        }

        Cache::put(
            CacheConstant::withPrefix(CacheConstant::AUTH_USER),
            $attributes,
            $this->setTTL($data["tokenExpires"])
        );

        Cache::put(
            CacheConstant::withPrefix(CacheConstant::AUTH_TOKEN),
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
        $diff = $date->diffInSeconds(now());

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
        Cache::forget(CacheConstant::withPrefix(CacheConstant::AUTH_TOKEN));
        Cache::forget(CacheConstant::withPrefix(CacheConstant::AUTH_USER));
    }

    public function login(array $attributes): void
    {
        $user = app(User::class, compact('attributes'));
        Auth::login($user);
    }
}