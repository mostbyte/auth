<?php

declare(strict_types=1);

namespace Mostbyte\Auth\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int    $id
 * @property-read string $name
 * @property-read string $nameUz
 * @property-read string $nameRu
 * @property-read string $nameEng
 */
class Role extends Model
{
    protected $guarded = [];

    /**
     * @return array
     */
    public static function attributes(): array
    {
        return [
            "id" => 2,
            "name" => "owner",
            "nameUz" => "Owner uz",
            "nameRu" => "Owner ru",
            "nameEng" => "Owner eng",
        ];
    }
}