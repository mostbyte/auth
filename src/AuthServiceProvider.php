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
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     * @throws BindingResolutionException
     */
    public function boot(): void
    {
        if ($this->app->environment(['local'])) {
            $base_url = config('mostbyte-auth.identity.base');

            Http::fake([
                $base_url . "/auth/check-token" => Http::response($this->fakeResponse())
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
                "tokenExpires" => "2022-11-03T11:02:01.972744Z",
                "user" => User::attributes()
            ]
        ];
    }
}