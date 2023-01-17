<?php

namespace Mostbyte\Auth\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'nameUz',
        'nameRu',
        'nameEng'
    ];

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
            "nameEng" => "Owner eng"
        ];
    }
}