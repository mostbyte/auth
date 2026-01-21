<?php

namespace MATests;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Http;
use Mostbyte\Auth\AuthServiceProvider;
use Mostbyte\Auth\Middleware\IdentityAuth;
use Mostbyte\Auth\Models\Branch;
use Mostbyte\Auth\Models\Company;
use Mostbyte\Auth\Models\Role;
use Mostbyte\Auth\Models\User;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /**
     * @throws BindingResolutionException
     */
    protected function setUp(): void
    {
        parent::setUp();

        Http::fake([
            app('identity')->getPath("auth/check-token") . "*" => Http::response($this->fakeResponse()),
        ]);
    }

    protected function getEnvironmentSetUp($app): void
    {
        $this->setRoutes($app['router']);

        $this->setConfig($app['config']);
    }

    protected function setRoutes(Router $router): void
    {
        $router->get('get-data', function () {
            return response([
                'data' => "Data",
                'success' => true,
                'message' => "Identity works correctly",
            ]);
        })->middleware(['api', IdentityAuth::class]);
    }

    protected function setConfig(Repository $config): void
    {
        $config->set('mostbyte-auth', require __DIR__ . "/../config/mostbyte-auth.php");
    }

    protected function getPackageProviders($app): array
    {
        return [AuthServiceProvider::class];
    }

    /**
     * @param bool $with_token
     * @return string[]
     */
    protected function headers(bool $with_token = true): array
    {
        $headers = [
            'Accept' => 'application/json',
        ];

        if ($with_token) {
            $headers = array_merge(
                $headers,
                ['Authorization' => app(User::class)->getToken()]
            );
        }

        return $headers;
    }

    /**
     * Get a fake response for check token route
     *
     * @return array
     */
    private function fakeResponse(): array
    {
        return [
            "success" => true,
            "message" => "Token is valid!",
            "data" => [
                "token" => app(User::class)->getToken(),
                "refreshToken" => "xsdRkyecDBbCdgchEbAc7X3RKiA3xH2UYzLf3ClW03gvoKrsGFPmYKBZwgWJMD1QSd3TeEiIEyzjtwERv8Sjdw==",
                "tokenExpires" => now()->addHours(2)->toISOString(),
                "user" => [
                    ...User::attributes(),
                    "company" => Company::attributes(),
                    "branch" => [
                        ...Branch::attributes(),
                        'company' => Company::attributes(),
                    ],
                    "role" => Role::attributes(),
                ],
            ],
        ];
    }
}