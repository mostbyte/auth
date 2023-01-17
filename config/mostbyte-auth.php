<?php
return [
    /*
    |----------------------------------------------
    | This is identity service API configurations
    |----------------------------------------------
    |
     */
    'identity' => [
        /*
        |-----------------------------------------
        | Identity service base url
        |-----------------------------------------
        |
        |
         */
        'base_url' => env('IDENTITY_BASE_URL', 'https://auth.mostbyte.uz'),

        /*
        |-----------------------------------------
        | API version
        |-----------------------------------------
         */
        'version' => 'v1',

        /*
        |-----------------------------------------
        | Headers
        |-----------------------------------------
        |
         */
        'headers' => [
            'Accept' => 'application/json'
        ],
    ],

    /*
    |---------------------------------------------
    | Authorization duration time
    |---------------------------------------------
    |
    | Authorization duration time in seconds, in default it is 2 hours, given in seconds
    |
     */
    'ttl' => 60 * 60 * 2
];