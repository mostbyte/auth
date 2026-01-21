<?php

declare(strict_types=1);

namespace Mostbyte\Auth;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;
use Mostbyte\Auth\Models\Branch;
use Mostbyte\Auth\Models\Company;
use Mostbyte\Auth\Models\Role;
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
        $this->registerPublishes();
        $this->registerFakeResponse();
    }

    protected function registerPublishes(): void
    {
        $this->publishes([
            __DIR__ . "/../config/mostbyte-auth.php" => config_path("mostbyte-auth.php"),
        ], "config");
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