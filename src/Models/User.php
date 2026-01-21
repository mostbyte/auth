<?php

declare(strict_types=1);

namespace Mostbyte\Auth\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticable;
use Mostbyte\Auth\Traits\Tokens;

/**
 * @property-read string          $uuid
 * @property-read string          $userName
 * @property-read string          $firstName
 * @property-read string          $surname
 * @property-read string          $patronymic
 * @property-read string          $email
 * @property-read string          $phoneNumber
 * @property-read int|null        $companyId
 * @property-read int|null        $branchId
 * @property-read int             $roleId
 * @property-read CarbonInterface $createdAt
 * @property-read CarbonInterface $updatedAt
 * @property-read string|null     $deletedAt
 * @property-read bool            $isActive
 * @property-read Company|null    $company
 * @property-read Branch|null     $branch
 * @property-read Role            $role
 */
class User extends Authenticable
{
    use Tokens;

    protected $primaryKey = 'uuid';
    protected $keyType = 'string';

    protected $guarded = ['uuid'];

    /**
     * @return array
     */
    public static function attributes(): array
    {
        return [
            "uuid" => "b011826c-d530-49e3-8374-4f6904c53633",
            "userName" => "testuser",
            "firstName" => "Test",
            "surname" => "User",
            "patronymic" => "Testovich",
            "email" => "test@example.com",
            "phoneNumber" => "+998901234567",
            "createdAt" => "2023-04-06T15:43:33.091392Z",
            "updatedAt" => "2023-04-06T15:43:33.091392Z",
            "deletedAt" => null,
            "isActive" => true,
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}