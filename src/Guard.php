<?php

declare(strict_types=1);

namespace Mostbyte\Auth;

use Mostbyte\Auth\Exceptions\InvalidTokenException;
use Illuminate\Contracts\Auth\Factory;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Mostbyte\Auth\Traits\LoginUser;

class Guard
{
    use LoginUser;

    public function __construct(
        protected Factory $auth,
        protected         $provider = null,
    ) {}

    public function __invoke(Request $request): ?Models\User
    {
        $token = $this->getTokenFromRequest($request);

        if (!$token) {
            return null;
        }

        try {
            $attributes = $this->prepareAttributesForLogin($token);
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