<?php

namespace src;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . "/../config/mostbyte-auth.php", "mostbyte-auth");
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->environment(['local'])) {

            $base_url = config('services.identity.api_v1');

            Http::fake([
                $base_url . "/auth/check-token" => Http::response($this->fakeResponse())
            ]);
        }
    }
}