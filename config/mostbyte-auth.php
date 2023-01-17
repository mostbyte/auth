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
    'ttl' => 60 * 60 * 2,

    /*
    |----------------------------------------------
    | Local development
    |----------------------------------------------
    |
    | If local development is "true", your all auth check requests will be used with fake responses.
    | In production you should specify it as "false"
    |
     */
    'local_development' => env('LOCAL_DEVELOPMENT', true)
];