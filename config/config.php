<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Force Two-Factor Authentication
    |--------------------------------------------------------------------------
    |
    | Force enable two-factor authentication when logging in, if the user has it set up.
    |
    */
    'force_two_factor_auth' => null,

    /*
    |--------------------------------------------------------------------------
    | Allow user registration
    |--------------------------------------------------------------------------
    |
    | If this is disabled users can only be created by administrators.
    |
    */

    'allow_registration' => true,

    /*
    |--------------------------------------------------------------------------
    | Prevent concurrent sessions
    |--------------------------------------------------------------------------
    |
    | When enabled users cannot sign in to multiple devices at the same time.
    |
    */

    'block_persistence' => false,

    /*
    |--------------------------------------------------------------------------
    | Login attribute
    |--------------------------------------------------------------------------
    |
    | Select what primary user detail should be used for signing in.
    |
    | email       Authenticate users by email.
    | username    Authenticate users by username.
    |
    */

    'login_attribute' => 'email',

    /*
    |--------------------------------------------------------------------------
    | Password Policy
    |--------------------------------------------------------------------------
    |
    | Specify the password policy for backend administrators.
    |
    | min_length            - Password minimum length between 4 - 128 chars
    | require_mixed_case    - Require at least one uppercase and lowercase letter
    | require_uncompromised - Require a password not found in a leaked password database
    | require_number        - Require at least one number
    | require_symbol        - Require at least one non-alphanumeric character
    |
    */

    'password_policy' => [
        'min_length' => 8,
        'require_mixed_case' => false,
        'require_uncompromised' => false,
        'require_number' => true,
        'require_symbol' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | JWT Config
    |--------------------------------------------------------------------------
    |
    | The token might be consumed in other systems. perhaps we could have a
    | few variables here and there to have control over the token variables.
    | Setting the value to null will leave it as the internal default value.
    |
    | - algorithm: see https://datatracker.ietf.org/doc/html/draft-ietf-jose-json-web-algorithms-40
    | - key: to be used instead of the app.key value
    | - ttl: in minutes
    | - leeway: in seconds
    |
    */

    'bearer_token' => [
        'algorithm' => null,
        'key' => null,
        'ttl' => null,
        'leeway' => null,
    ]
];
