<?php

use RainLab\User\Models\Setting;

return [

    /*
    |--------------------------------------------------------------------------
    | Activation mode
    |--------------------------------------------------------------------------
    |
    | Select how a user account should be activated.
    |
    | ACTIVATE_ADMIN    Administrators must activate users manually.
    | ACTIVATE_AUTO     Users are activated automatically upon registration.
    | ACTIVATE_USER     The user activates their own account using a link sent to them via email.
    |
    */

    'activateMode' => Setting::ACTIVATE_AUTO,

    /*
    |--------------------------------------------------------------------------
    | Allow user registration
    |--------------------------------------------------------------------------
    |
    | If this is disabled users can only be created by administrators.
    |
    */

    'allowRegistration' => true,

    /*
    |--------------------------------------------------------------------------
    | Prevent concurrent sessions
    |--------------------------------------------------------------------------
    |
    | When enabled users cannot sign in to multiple devices at the same time.
    |
    */

    'blockPersistence' => false,

    /*
    |--------------------------------------------------------------------------
    | Login attribute
    |--------------------------------------------------------------------------
    |
    | Select what primary user detail should be used for signing in.
    |
    | LOGIN_EMAIL       Authenticate users by email.
    | LOGIN_USERNAME    Authenticate users by username.
    |
    */

    'loginAttribute' => Setting::LOGIN_EMAIL,

    /*
    |--------------------------------------------------------------------------
    | Minimum Password Length
    |--------------------------------------------------------------------------
    |
    | The minimum length of characters required for user passwords.
    |
    */

    'minPasswordLength' => 8,

    /*
    |--------------------------------------------------------------------------
    | Remember login mode
    |--------------------------------------------------------------------------
    |
    | Select if the user session should be persistent.
    |
    | REMEMBER_ALWAYS   Always persist user session.
    | REMEMBER_ASK      Ask if session should be persistent.
    | REMEMBER_NEVER    Never persist user session.
    |
    */

    'rememberLogin' => Setting::REMEMBER_ALWAYS,

    /*
    |--------------------------------------------------------------------------
    | Sign in requires activation
    |--------------------------------------------------------------------------
    |
    | Users must have an activated account to sign in.
    |
    */

    'requireActivation' => true,

    /*
    |--------------------------------------------------------------------------
    | Throttle registration
    |--------------------------------------------------------------------------
    |
    | Prevent multiple registrations from the same IP in short succession.
    |
    */

    'useRegisterThrottle' => true,

    /*
    |--------------------------------------------------------------------------
    | Throttle attempts
    |--------------------------------------------------------------------------
    |
    | Repeat failed sign in attempts will temporarily suspend the user.
    |
    */

    'useThrottle' => true,

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

    'bearerToken' => [
        'algorithm' => null,
        'key' => null,
        'ttl' => null,
        'leeway' => null,
    ]
];
