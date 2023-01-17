<?php

namespace Mostbyte\Auth\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Mostbyte\Auth\Models\Role;

class RoleCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param Model $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return mixed
     */
    public function get($model, string $key, $value, array $attributes): mixed
    {
        return $value;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param Model $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return Role[]
     */
    public function set($model, string $key, $value, array $attributes): array
    {
        return [
            $key => $value instanceof Role ? $value : new Role($value)
        ];
    }
}