<?php

namespace Mostbyte\Auth;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Mostbyte\Auth\Exceptions\InvalidTokenException;

class Identity
{
    /**
     * @var string base url of identity service
     */
    private string $base_url;

    /**
     * @var string url of identity service
     */
    private string $url;


    public function __construct()
    {
        $this->base_url = config("mostbyte-auth.identity.base_url");

        $version = config("mostbyte-auth.identity.version");

        $this->url = sprintf("%s/%s", $this->base_url, $version);
    }

    /**
     * Get path
     *
     * @param string $path
     * @return string
     */
    public function getPath(string $path): string
    {
        $path = Str::of($path)->trim("/");

        return sprintf("%s/%s", $this->url, $path);
    }

    /**
     * Check token is valid or not
     *
     * @throws RequestException
     */
    public function checkToken($token)
    {
        $headers = array_merge(
            config('mostbyte-auth.identity.headers'),
            ['Authorization' => $token]
        );

        return Http::withHeaders($headers)
            ->post($this->getPath('auth/check-token'))
            ->throw()
            ->json('data');
    }
}