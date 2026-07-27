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
            'Accept' => 'application/json',
        ],
    ],

    /*
     |--------------------------------------------
     | Guard
     |--------------------------------------------
     |
     | Authorization guard
     |
     */
    'guard' => ['identity'],

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
    | If local development is "true", all auth check requests are answered with
    | fake responses — every token is accepted as a valid superUser. Because that
    | is a full authorization bypass, the default is "false": a service that never
    | sets LOCAL_DEVELOPMENT stays secure. Opt in explicitly with
    | LOCAL_DEVELOPMENT=true in your local .env, and never in a deployed .env.
    |
     */
    'local_development' => env('LOCAL_DEVELOPMENT', false),
];