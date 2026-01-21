<?php

declare(strict_types=1);

namespace Mostbyte\Auth\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read int             $id
 * @property-read int             $companyId
 * @property-read string|null     $nameUz
 * @property-read string|null     $nameRu
 * @property-read string|null     $nameEng
 * @property-read CarbonInterface $createdAt
 * @property-read CarbonInterface $updatedAt
 * @property-read string|null     $imagePath
 * @property-read float           $latitude
 * @property-read float           $longitude
 * @property-read Company         $company
 */
class Branch extends Model
{
    protected $guarded = [];

    /**
     * @return array
     */
    public static function attributes(): array
    {
        return [
            "id" => 1,
            "nameUz" => "Test Branch Uz",
            "nameRu" => "Test Branch Ru",
            "nameEng" => "Test Branch Eng",
            "createdAt" => "2023-04-06T15:51:12.680369Z",
            "updatedAt" => "2023-04-06T15:51:12.680369Z",
            "imagePath" => null,
            "latitude" => 41.311081,
            "longitude" => 69.240562,
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}