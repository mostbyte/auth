<?php

declare(strict_types=1);

namespace Mostbyte\Auth\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Mostbyte\Auth\Exceptions\InvalidTokenException;
use Mostbyte\Auth\Models\Branch;
use Mostbyte\Auth\Models\Company;
use Mostbyte\Auth\Models\Role;
use Mostbyte\Auth\Models\User;
use Mostbyte\Auth\Traits\LoginUser;
use Throwable;

class IdentityAuth
{
    use LoginUser;

    public static function using(?string $args = null): string
    {
        return static::class . ($args ? ":$args" : '');
    }

    /**
     * Handle an incoming request.
     *
     * @param Request     $request
     * @param Closure     $next
     * @param string|null $args
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next, ?string $args = null): mixed
    {
        try {
            $token = $request->header('Authorization');

            $attributes = $this->prepareAttributesForLogin($token, $args);

            $this->login($attributes);
        } catch (InvalidTokenException $e) {
            $this->clearCache();

            report($e);

            return response([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        } catch (ConnectionException $e) {
            report($e);

            return response([
                'success' => false,
                'message' => 'Auth service is not responding',
            ], 401);
        } catch (Throwable $e) {
            report($e);

            return response([
                'success' => false,
                'message' => $e->getMessage(),
            ], 401);
        }

        return $next($request);
    }

    protected function getUser(array $attributes): User
    {
        $userAttributes = $this->castDates($attributes);
        $userAttributes['companyId'] = Arr::get($userAttributes, 'company.id');
        $userAttributes['roleId'] = Arr::get($userAttributes, 'role.id');
        $userAttributes['branchId'] = Arr::get($userAttributes, 'branch.id');

        $user = new User(Arr::except($userAttributes, ['company', 'role', 'branch']));

        if (isset($attributes['company'])) {
            $user->setRelation('company', new Company($this->castDates($attributes['company'])));
        }

        $user->setRelation('role', new Role($attributes['role']));

        if (isset($attributes['branch'])) {
            $branchAttributes = Arr::except($attributes['branch'], ['company']);
            $branchAttributes['companyId'] = Arr::get($branchAttributes, 'company.id');
            $branch = new Branch($this->castDates($branchAttributes));

            if (isset($attributes['branch']['company'])) {
                $branch->setRelation('company', new Company($this->castDates($attributes['branch']['company'])));
            }

            $user->setRelation('branch', $branch);
        }

        return $user;
    }

    /**
     * Cast date strings to Carbon instances
     */
    protected function castDates(array $attributes): array
    {
        $dateFields = ['createdAt', 'updatedAt'];

        foreach ($dateFields as $field) {
            if (isset($attributes[$field]) && is_string($attributes[$field])) {
                $attributes[$field] = Carbon::parse($attributes[$field]);
            }
        }

        return $attributes;
    }
}
