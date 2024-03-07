<?php namespace RainLab\User\Helpers;

use RainLab\User\Models\Setting as UserSetting;
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
     * showUsername
     */
    public static function showUsername()
    {
        return static::username() === UserSetting::LOGIN_USERNAME;
    }

    /**
     * username returns the login attribute to use for logging in.
     */
    public static function username()
    {
        return UserSetting::get('login_attribute', UserSetting::LOGIN_EMAIL);
    }

    /**
     * passwordRules returns rules for the password field
     */
    public static function passwordRules()
    {
        return ['required', 'string', UserSetting::makePasswordRule(), 'confirmed'];
    }
}
