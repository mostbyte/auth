<?php

namespace Mostbyte\Auth\Middleware;

use Closure;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mostbyte\Auth\Exceptions\InvalidTokenException;
use Mostbyte\Auth\Traits\LoginUser;
use Throwable;

class IdentityAuth
{
    use LoginUser;

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response|RedirectResponse
     * @throws RequestException
     */
    public function handle(Request $request, Closure $next): mixed
    {
        try {
            $token = $request->header('Authorization');

            $attributes = $this->prepareAttributesForLogin($token);

            $this->login($attributes);

        } catch (InvalidTokenException $exception) {

            $this->clearCache();

            return response([
                'success' => false,
                'message' => 'Unauthorized'
            ]);
        }

        return $next($request);
    }
}