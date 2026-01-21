<?php

declare(strict_types=1);

namespace Mostbyte\Auth\Enums;

enum CacheKey: string
{

    case AUTH_TOKEN = "auth-token";
    case AUTH_USER = "auth-user";

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
     * Get a cache key with prefix ip and user-agent
     *
     * @param mixed ...$suffix
     * @return string
     */
    public function withPrefix(...$suffix): string
    {
        $company = identity()->getCompany();

        $suffix[] = request()->ip();
        $suffix[] = request()->header('device-id', "some-device-id");

        $suffix = implode('-', $suffix);

        return sprintf(
            '%s-%s-%s',
            $company,
            $this->value,
            $suffix
        );
    }
}
