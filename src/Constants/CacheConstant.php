<?php

namespace Mostbyte\Auth\Constants;

class CacheConstant
{

    const AUTH_TOKEN = "auth-token";
    const AUTH_USER = "auth-user";

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
     * @param string|array $suffix
     * @return string
     */
    public static function withPrefix(string $key, ...$suffix): string
    {
        $company = identity()->getCompany();

        $suffix[] = request()->ip();

        $suffix = implode('-', $suffix);

        return sprintf(
            '%s-%s-%s',
            $company,
            $key,
            $suffix
        );
    }
}