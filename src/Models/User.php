<?php

namespace Mostbyte\Auth\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticable;
use Mostbyte\Auth\Traits\Tokens;

/**
 * @property-read Company $company
 * @property-read Role $role
 */
class User extends Authenticable
{
    use Tokens;

    protected $primaryKey = 'uuid';
    protected $keyType = 'string';

    protected $fillable = [
        'uuid',
        'username',
        'firstName',
        'surname',
        'patronymic',
        'email',
        'branch',
        'createdAt',
        'updatedAt',
    ];

    /**
     * @return array
     */
    public static function attributes(): array
    {
        return [
            "uuid" => "b011826c-d530-49e3-8374-4f6904c53633",
            "username" => "testtest",
            "firstName" => "Test",
            "surname" => "Test",
            "patronymic" => "Test",
            "email" => "testtest@test.test",
            "company" => Company::attributes(),
            "branch" => [
                "id" => 1
            ],
            "createdAt" => "2022-10-21T09:32:33.255876Z",
            "updatedAt" => "2022-10-21T09:32:33.255876Z",
            "role" => Role::attributes()
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}