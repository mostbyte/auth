<?php

namespace MATests\Middleware;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use MATests\TestCase;
use Mostbyte\Auth\Middleware\IdentityAuth;
use Mostbyte\Auth\Models\Branch;
use Mostbyte\Auth\Models\Company;
use Mostbyte\Auth\Models\Role;
use Mostbyte\Auth\Models\User;

class TokenCacheTest extends TestCase
{
    private function fakeByToken(array $usersByToken): void
    {
        Http::swap(new \Illuminate\Http\Client\Factory());
        Http::fake(function ($request) use ($usersByToken) {
            $token = preg_replace('/^Bearer\s+/i', '', $request->header('Authorization')[0] ?? '');

            if (!isset($usersByToken[$token])) {
                return Http::response(['success' => false], 401);
            }

            return Http::response([
                'success' => true,
                'data' => [
                    'tokenExpires' => now()->addHours(2)->toISOString(),
                    'user' => [
                        ...User::attributes(),
                        'uuid' => $usersByToken[$token],
                        'company' => Company::attributes(),
                        'branch' => [...Branch::attributes(), 'company' => Company::attributes()],
                        'role' => Role::attributes(),
                    ],
                ],
            ]);
        });
    }

    private function callAs(string $token, string $path = 'whoami')
    {
        // один ip и один device-id у всех запросов
        return $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.5'])
            ->getJson($path, ['Authorization' => "Bearer $token", 'device-id' => 'same-device']);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->app['router']->get('whoami', fn() => response()->json([
            'id' => auth()->user()->uuid,
            'token' => auth()->user()->getToken(),
        ]))->middleware(['api', IdentityAuth::class]);
    }

    public function test_two_tokens_same_ip_and_device_get_own_users(): void
    {
        $this->fakeByToken(['tok-a' => 'user-a', 'tok-b' => 'user-b']);

        $this->callAs('tok-a')->assertOk()->assertJson(['id' => 'user-a', 'token' => 'Bearer tok-a']);
        $this->callAs('tok-b')->assertOk()->assertJson(['id' => 'user-b', 'token' => 'Bearer tok-b']);
        // повтор уже из кэша
        $this->callAs('tok-a')->assertOk()->assertJson(['id' => 'user-a', 'token' => 'Bearer tok-a']);
        $this->callAs('tok-b')->assertOk()->assertJson(['id' => 'user-b', 'token' => 'Bearer tok-b']);
    }

    public function test_cached_user_is_not_applied_to_other_token(): void
    {
        $this->fakeByToken(['tok-a' => 'user-a']);

        $this->callAs('tok-a')->assertOk();

        // токен B неизвестен identity: кэш токена A ему не помогает
        $this->callAs('tok-b')->assertUnauthorized()->assertJson(['message' => 'Unauthorized']);
    }

    public function test_cache_stores_no_raw_token(): void
    {
        $this->fakeByToken(['tok-a' => 'user-a']);

        $this->callAs('tok-a')->assertOk();

        $key = 'mostbyte-auth-' . hash('sha256', 'Bearer tok-a');
        $this->assertFalse(Cache::has($key));
        $this->assertTrue(Cache::has($this->keyFor('tok-a')));
    }

    private function keyFor(string $token): string
    {
        return \Mostbyte\Auth\Enums\CacheKey::AUTH_USER->withToken($token);
    }

    public function test_invalid_token_clears_cache(): void
    {
        $this->fakeByToken([]);

        $this->callAs('tok-x')->assertUnauthorized();
        $this->assertFalse(Cache::has($this->keyFor('tok-x')));
    }
}
