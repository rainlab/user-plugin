<?php namespace RainLab\User\Helpers;

use RainLab\User\Models\Settings as UserSettings;
use RainLab\User\Classes\Validation\PasswordRule;

/**
 * User defines some helpers for the user module
 *
 * @package rainlab\user
 * @author Alexey Bobkov, Samuel Georges
 */
class User
{
    /**
     * username returns the login attribute to use for logging in.
     */
    public static function username()
    {
        // @todo config
        // UserSettings::get('login_attribute', UserSettings::LOGIN_EMAIL)
        return 'email';
    }

    /**
     * passwordRules returns rules for the password field
     */
    public static function passwordRules()
    {
        // @todo config
        return ['required', 'string', new PasswordRule, 'confirmed'];
    }
}
