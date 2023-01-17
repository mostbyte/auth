<?php

namespace Mostbyte\Auth\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mostbyte\Auth\Traits\LoginUser;
use Throwable;

class IdentityAuth
{
    use LoginUser;

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        try {
            $token = $request->header('Authorization');

            $attributes = $this->prepareAttributesForLogin($token, $this->checkTokens($token));

            $this->login($attributes);

        } catch (Throwable) {

            $this->clearCache();

            return response([
                'success' => false,
                'message' => 'Unauthorized'
            ]);
        }

        return $next($request);
    }
}