<?php

namespace Mostbyte\Auth\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "id",
        "name",
        "address",
        "inn",
        "createdAt",
        "updatedAt",
        "domain",
        "type",
        "licenseKey"
    ];

    /**
     * @return array
     */
    public static function attributes(): array
    {
        return [
            "id" => 1,
            "name" => "nulla dolor dolore id",
            "address" => "ullamco labore et",
            "inn" => "1234567890",
            "createdAt" => "2023-01-07T10:40:50.424676Z",
            "updatedAt" => "2023-01-07T10:40:50.424676Z",
            "domain" => "consequat mollit",
            "type" => "eni",
            "licenseKey" => "UBtsZQS0k2UqsPRGefEil2Hi",
        ];
    }
}