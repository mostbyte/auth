<?php

namespace Mostbyte\Auth\Constants;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Request;

class CacheConstant
{

    const AUTH_TOKEN = "auth.token";
    const AUTH_USER = "auth.user";

    /**
     * Auth TTL time
     *
     * @return int
     */
    public static function ttl(): int
    {
        return config('mostbyte-auth.ttl', 60 * 60 * 2);
    }

    /**
     * Get cache key with prefix ip and user-agent
     *
     * @param string $key
     * @param string $suffix
     * @return string
     */
    public static function withPrefix(string $key, string $suffix = ''): string
    {
        return sprintf(
            '%s-%s-%s-%s',
            Request::ip(),
            Request::userAgent(),
            $key,
            $suffix
        );
    }
}