<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Authentication Credentials
    |--------------------------------------------------------------------------
    |
    | These credentials are used for authenticating API requests
    | from the QC service.
    |
    */
    'auth' => [
        'username' => env('API_AUTH_USERNAME', 'qc_user'),
        'password' => env('API_AUTH_PASSWORD', 'qc_password'),
    ],
    'user_id' => env('API_USER_ID', 18),   
];
