<?php

declare(strict_types=1);

namespace Mostbyte\Auth\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int             $id
 * @property-read string          $name
 * @property-read string|null     $address
 * @property-read string|null     $inn
 * @property-read CarbonInterface $createdAt
 * @property-read CarbonInterface $updatedAt
 * @property-read string          $domain
 * @property-read string          $type
 * @property-read string          $licenseKey
 * @property-read string|null     $imagePath
 * @property-read bool            $isActive
 */
class Company extends Model
{
    protected $guarded = ['id'];

    /**
     * @return array
     */
    public static function attributes(): array
    {
        return [
            "id" => 1,
            "name" => "Test Company",
            "address" => "test-address",
            "inn" => "12345678",
            "createdAt" => "2023-04-06T15:51:12.680369Z",
            "updatedAt" => "2023-04-06T15:51:12.680369Z",
            "domain" => "testcompany",
            "type" => "cafe",
            "licenseKey" => "nMDp6tBfZpEroehEmlTRe0u8",
            "imagePath" => "340484c4-75b8-4b53-8cda-8dd5ac10f3dd.png",
            "isActive" => true,
        ];
    }
}