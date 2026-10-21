<?php

declare(strict_types=1);

namespace Mostbyte\Auth\Enums;

enum CacheKey: string
{

    case AUTH_USER = "auth";

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
     * Ключ кэша пользователя: компания + sha256 токена (+ args). Сам токен в ключ не попадает.
     */
    public function withToken(string $token, ?string $args = null): string
    {
        return sprintf(
            '%s-%s-%s%s',
            identity()->getCompany(),
            $this->value,
            hash('sha256', $token),
            $args ? "-$args" : ''
        );
    }
}
