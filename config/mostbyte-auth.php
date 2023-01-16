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
        'base_url' => env('IDENTITY_BASE_URL'),

        /*
        |-----------------------------------------
        | Headers
        |-----------------------------------------
        |
         */
        'headers' => [
            'Accept' => 'application/json'
        ],
    ]
];