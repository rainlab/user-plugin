<?php

use RainLab\User\Models\Settings;

return [

    /*
    |--------------------------------------------------------------------------
    | Activation mode
    |--------------------------------------------------------------------------
    |
    | Select how a user account should be activated.
    |
    */

    'activateMode' => Settings::ACTIVATE_AUTO,

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
    */

    'loginAttribute' => Settings::LOGIN_EMAIL,

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
    */

    'rememberLogin' => Settings::REMEMBER_ALWAYS,

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
];
