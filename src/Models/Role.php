<?php

namespace Mostbyte\Auth\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{

    public static function attributes(): array
    {
        return [
            "id" => 2,
            "name" => "owner",
            "nameUz" => "Owner uz",
            "nameRu" => "Owner ru",
            "nameEng" => "Owner eng"
        ];
    }
}