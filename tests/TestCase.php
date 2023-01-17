<?php

namespace Tests;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Http;
use Mostbyte\Auth\AuthServiceProvider;
use Mostbyte\Auth\Middleware\IdentityAuth;
use Mostbyte\Auth\Models\User;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Http::fake([
            app('identity')->getPath("auth/check-token") => Http::response($this->fakeResponse())
        ]);
    }

    protected function getEnvironmentSetUp($app)
    {
        $this->setRoutes($app['router']);

        $this->setConfig($app['config']);
    }

    protected function setRoutes(Router $router)
    {
        $router->get('get-data', function () {

            return response([
                'data' => "Data",
                'success' => true,
                'message' => "Identity works correctly"
            ]);

        })->middleware(['api', IdentityAuth::class]);
    }

    protected function setConfig(Repository $config)
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
            'Accept' => 'application/json'
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
     * Get fake response for check token route
     *
     * @return array
     * @throws BindingResolutionException
     */
    private function fakeResponse(): array
    {
        return [
            "success" => true,
            "message" => "Token is valid!",
            "data" => [
                "token" => app(User::class)->getToken(),
                "refreshToken" => "s6PLorf3T1kqI84Dj9+fpwhIJc3n3pvrWqJytwXeoBy8GmH7WSDdk5ilFnXNjT5ThVm9m+UXMwJvNft9oAbECA==",
                "tokenExpires" => "2023-11-03T11:02:01.972744Z",
                "user" => User::attributes()
            ]
        ];
    }
}