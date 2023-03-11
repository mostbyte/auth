<?php

namespace Mostbyte\Auth;

use Illuminate\Contracts\Container\BindingResolutionException;
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
        $this->mergeConfigFrom(__DIR__ . "/../config/mostbyte-auth.php", "mostbyte-auth");

        $this->registerHelpers();

        $this->app->singleton('identity', function ($app) {
            return new Identity($app['request']);
        });
    }

    protected function registerHelpers()
    {
        foreach (glob(__DIR__ . "/../helpers/*.php") as $helper) {
            require $helper;
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     * @throws BindingResolutionException
     */
    public function boot(): void
    {
        $this->registerPublishes();
        $this->registerFakeResponse();
    }

    protected function registerPublishes()
    {
        $this->publishes([
            __DIR__ . "/../config/mostbyte-auth.php" => config_path("mostbyte-auth.php")
        ], "config");
    }

    /**
     * @throws BindingResolutionException
     */
    protected function registerFakeResponse()
    {
        if (config('mostbyte-auth.local_development')) {

            Http::fake([
                identity("auth/check-token") . "*" => Http::response($this->fakeResponse())
            ]);
        }
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