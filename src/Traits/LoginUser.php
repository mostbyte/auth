<?php

declare(strict_types=1);

namespace Mostbyte\Auth\Traits;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Mostbyte\Auth\Enums\CacheKey;
use Mostbyte\Auth\Exceptions\InvalidTokenException;
use Mostbyte\Auth\Models\Branch;
use Mostbyte\Auth\Models\Company;
use Mostbyte\Auth\Models\Role;
use Mostbyte\Auth\Models\User;

trait LoginUser
{
    /**
     * Токен текущего вызова: нужен для clearCache() без аргумента (совместимость).
     * Перезаписывается при каждом prepareAttributesForLogin и сбрасывается после очистки.
     */
    /** TTL кэша для токена без tokenExpires, секунды */
    private const EMPTY_EXPIRES_TTL = 600;

    private ?string $currentToken = null;

    private ?string $currentArgs = null;

    /**
     * Есть ли в кэше атрибуты пользователя для этого токена
     */
    public function checkTokens(string $token, ?string $args = null): bool
    {
        return Cache::has($this->cacheKey($token, $args));
    }

    /**
     * Ключ кэша привязан к хэшу токена, а не к ip/device-id (их контролирует клиент или NAT).
     * $args учитывается: no-domain даёт другой ответ check-token.
     */
    protected function cacheKey(string $token, ?string $args = null): string
    {
        return CacheKey::AUTH_USER->withToken($this->normalizeToken($token), $args);
    }

    protected function normalizeToken(string $token): string
    {
        return trim(preg_replace('/^bearer\s+/i', '', trim($token)));
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
        $this->currentToken = filled($token) ? $token : null;
        $this->currentArgs = $args;

        if (blank($token)) {
            $this->forceStop('Token is empty');
        }

        $key = $this->cacheKey($token, $args);

        $cached = Cache::get($key);

        if (is_array($cached) && $cached !== []) {
            return $cached;
        }

        $data = identity()->checkToken($token, $args);

        $attributes = $data['user'];

        Cache::put($key, $attributes, $this->setTTL($data['tokenExpires'] ?? null));

        return $attributes;
    }

    /**
     * tokenExpires может отсутствовать (бессрочный токен): кэшируем коротко, не на полный TTL
     */
    protected function setTTL(?string $timestamp): int
    {
        if (blank($timestamp)) {
            return min(self::EMPTY_EXPIRES_TTL, CacheKey::ttl());
        }

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
    public function forceStop(string $message = '', ?string $token = null): void
    {
        $this->clearCache($token);

        throw new InvalidTokenException($message);
    }

    /**
     * Сбросить кэш токена; без аргумента - токен последнего prepareAttributesForLogin
     */
    public function clearCache(?string $token = null, ?string $args = null): void
    {
        $token ??= $this->currentToken;
        $args ??= $this->currentArgs;

        $this->currentToken = null;
        $this->currentArgs = null;

        if (blank($token)) {
            return;
        }

        Cache::forget($this->cacheKey($token, $args));
    }

    /**
     * @param array       $attributes
     * @param string|null $token токен из запроса; без него войти нельзя
     * @throws InvalidTokenException
     */
    public function login(array $attributes, ?string $token = null): void
    {
        $token ??= $this->currentToken;

        if (blank($token)) {
            $this->forceStop("Token is empty");
        }

        Auth::login($this->getUser($attributes));

        /** @var User $user */
        $user = Auth::user();

        $user->setToken($token);

        // токен не должен перетечь в следующий вызов в долгоживущем процессе
        $this->currentToken = null;
        $this->currentArgs = null;
    }

    protected function getUser(array $attributes): User
    {
        $userAttributes = $this->castDates($attributes);
        $userAttributes['companyId'] = Arr::get($userAttributes, 'company.id');
        $userAttributes['roleId'] = Arr::get($userAttributes, 'role.id');
        $userAttributes['branchId'] = Arr::get($userAttributes, 'branch.id');

        $user = new User(Arr::except($userAttributes, ['company', 'role', 'branch']));

        if (isset($attributes['company'])) {
            $user->setRelation('company', new Company($this->castDates($attributes['company'])));
        }

        $user->setRelation('role', new Role($attributes['role']));

        if (isset($attributes['branch'])) {
            $branchAttributes = Arr::except($attributes['branch'], ['company']);
            $branchAttributes['companyId'] = Arr::get($branchAttributes, 'company.id');
            $branch = new Branch($this->castDates($branchAttributes));

            if (isset($attributes['branch']['company'])) {
                $branch->setRelation('company', new Company($this->castDates($attributes['branch']['company'])));
            }

            $user->setRelation('branch', $branch);
        }

        return $user;
    }

    /**
     * Cast date strings to Carbon instances
     */
    protected function castDates(array $attributes): array
    {
        $dateFields = ['createdAt', 'updatedAt'];

        foreach ($dateFields as $field) {
            if (isset($attributes[$field]) && is_string($attributes[$field])) {
                $attributes[$field] = \Carbon\Carbon::parse($attributes[$field]);
            }
        }

        return $attributes;
    }
}
