<?php

declare(strict_types=1);

namespace Mostbyte\Auth;

use Illuminate\Contracts\Auth\Factory;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Mostbyte\Auth\Exceptions\InvalidTokenException;
use Mostbyte\Auth\Traits\LoginUser;

class Guard
{
    use LoginUser;

    public function __construct(
        protected Factory $auth,
        protected         $provider = null,
        protected bool    $no_domain = false
    ) {}

    public function __invoke(Request $request): ?Models\User
    {
        $token = $this->getTokenFromRequest($request);

        if (!$token) {
            return null;
        }

        try {
            $args = $this->no_domain ? 'no-domain' : null;

            $attributes = $this->prepareAttributesForLogin($token, $args);
        } catch (ConnectionException|InvalidTokenException $e) {
            $this->clearCache();
            report($e);

            return null;
        }

        return $this->getUser($attributes);
    }

    private function getTokenFromRequest(Request $request): ?string
    {
        return $request->bearerToken();
    }
}