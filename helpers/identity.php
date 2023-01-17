<?php
if (!function_exists('identity_url')) {
    /**
     * @param string $path
     * @return string
     */
    function identity_url(string $path = ''): string
    {
        $base_url = config('mostybte-auth.identity.base_url');
        $version = config('mostybte-auth.identity.version');

        return sprintf("%s/api/%s/%s", $base_url, $version, $path);

    }
}