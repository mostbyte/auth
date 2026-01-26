<?php

namespace Mostbyte\Auth;

use Illuminate\Auth\RequestGuard;
use Illuminate\Contracts\Auth\Factory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;
use Mostbyte\Auth\Models\User;

class AuthServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        config([
            'auth.guards.identity' => array_merge([
                'driver' => 'identity',
                'provider' => null,
            ], config('auth.guards.identity', [])),
        ]);

        if ($this->app->configurationIsCached()) {
            $this->mergeConfigFrom(__DIR__ . "/../config/mostbyte-auth.php", "mostbyte-auth");
        }

        $this->registerHelpers();

        $this->app->singleton('identity', function ($app) {
            return new Identity($app['request']);
        });
    }

    protected function registerHelpers(): void
    {
        foreach (glob(__DIR__ . "/../helpers/*.php") as $helper) {
            require $helper;
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->registerPublishes();
        }
        $this->configureGuard();
        $this->registerFakeResponse();
    }

    protected function registerPublishes(): void
    {
        $this->publishes([
            __DIR__ . "/../config/mostbyte-auth.php" => config_path("mostbyte-auth.php"),
        ], "config");
    }

    protected function configureGuard(): void
    {
        Auth::resolved(function (Factory $auth) {
            $auth->extend('identity', function ($app, $name, array $config) use ($auth) {
                return tap($this->createGuard($auth, $config), function ($guard) {
                    app()->refresh('request', $guard, 'setRequest');
                });
            });
        });
    }

    protected function createGuard(Factory $auth, array $config): RequestGuard
    {
        return new RequestGuard(
            new Guard(
                $auth,
                $config['provider'],
            ),
            request(),
            $auth->createUserProvider($config['provider'] ?? null)
        );
    }

    protected function registerFakeResponse(): void
    {
        if (config('mostbyte-auth.local_development')) {

            Http::fake([
                identity("auth/check-token") . "*" => Http::response($this->fakeResponse()),
            ]);
        }
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
                "refreshToken" => "s6PLorf3T1kqI84Dj9+fpwhIJc3n3pvrWqJytwXeoBy8GmH7WSDdk5ilFnXNjT5ThVm9m+UXMwJvNft9oAbECA==",
                "tokenExpires" => "2023-11-03T11:02:01.972744Z",
                "user" => User::attributes(),
            ],
        ];
    }
}