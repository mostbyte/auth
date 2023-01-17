<?php

use Mostbyte\Auth\Identity;

if (!function_exists('identity_url')) {
    /**
     * @param string $path
     * @return string|Identity
     */
    function identity(string $path = ''): string|Identity
    {

        if (!$path) {
            return app('identity');
        }

        return app('identity')->getPath($path);
    }
}