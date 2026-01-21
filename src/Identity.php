<?php

declare(strict_types=1);

namespace Mostbyte\Auth;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
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

    /**
     * @var string company name
     */
    private string $company;


    public function __construct(Request $request)
    {
        $this->base_url = config("mostbyte-auth.identity.base_url");

        $version = config("mostbyte-auth.identity.version");

        $this->url = sprintf("%s/api/%s", $this->base_url, $version);

        $this->company = Str::of(parse_url($request->url(), PHP_URL_PATH))->trim('/')->explode('/')[0];
    }

    /**
     * Get path
     *
     * @param string $path
     * @param array $parameters
     * @return string
     */
    public function getPath(string $path, array $parameters = []): string
    {
        $path = Str::of($path)->trim("/");

        $query_parameters = http_build_query($parameters);

        return sprintf("%s/%s?%s", $this->url, $path, $query_parameters);
    }

    /**
     * Check token is valid or not
     *
     * @param $token
     * @param string|null $args
     * @return array
     * @throws ConnectionException
     * @throws InvalidTokenException
     */
    public function checkToken($token, ?string $args = null): array
    {
        $headers = [
            'Authorization' => $token,
            ...config('mostbyte-auth.identity.headers')
        ];

        $path = $this->getPath('auth/check-token',
            $args === 'no-domain'
                ? []
                : ['domain' => $this->company]
        );

        $request = Http::withHeaders($headers)->post($path);

        if ($request->failed() || !$request->json('success')) {
            throw new InvalidTokenException();
        }

        return $request->json('data');
    }

    public function getCompany()
    {
        return $this->company;
    }
}
