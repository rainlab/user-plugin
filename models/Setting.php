<?php namespace RainLab\User\Models;

use Model;
use Config;

/**
 * Setting configuration
 */
class Setting extends Model
{
    /**
     * @var array Behaviors implemented by this model.
     */
    public $implement = [
        \System\Behaviors\SettingsModel::class
    ];

    public $settingsCode = 'user_settings';
    public $settingsFields = 'fields.yaml';

    const ACTIVATE_AUTO = 'auto';
    const ACTIVATE_USER = 'user';
    const ACTIVATE_ADMIN = 'admin';

    const LOGIN_EMAIL = 'email';
    const LOGIN_USERNAME = 'username';

    const REMEMBER_ALWAYS = 'always';
    const REMEMBER_NEVER = 'never';
    const REMEMBER_ASK = 'ask';

    /**
     * initSettingsData
     */
    public function initSettingsData()
    {
        $this->require_activation = Config::get('rainlab.user::requireActivation', false);
        $this->activate_mode = Config::get('rainlab.user::activateMode', self::ACTIVATE_AUTO);
        $this->block_persistence = Config::get('rainlab.user::blockPersistence', false);
        $this->allow_registration = Config::get('rainlab.user::allowRegistration', true);
        $this->login_attribute = Config::get('rainlab.user::loginAttribute', self::LOGIN_EMAIL);
        $this->remember_login = Config::get('rainlab.user::rememberLogin', self::REMEMBER_ALWAYS);
    }

    /**
     * getActivateModeOptions
     */
    public function getActivateModeOptions()
    {
        return [
            self::ACTIVATE_AUTO => [
                "Automatic",
                "Activated automatically upon registration."
            ],
            self::ACTIVATE_USER => [
                "User",
                "The user activates their own account using mail."
            ],
            self::ACTIVATE_ADMIN => [
                "Administrator",
                "Only an Administrator can activate a user."
            ]
        ];
    }

    /**
     * getActivateModeAttribute
     */
    public function getActivateModeAttribute($value)
    {
        if (!$value) {
            return self::ACTIVATE_AUTO;
        }

        return $value;
    }

    /**
     * getLoginAttributeOptions
     */
    public function getLoginAttributeOptions()
    {
        return [
            self::LOGIN_EMAIL => ["Email"],
            self::LOGIN_USERNAME => ["Username"]
        ];
    }

    /**
     * getRememberLoginOptions
     */
    public function getRememberLoginOptions()
    {
        return [
            self::REMEMBER_ALWAYS => [
                "Always",
            ],
            self::REMEMBER_NEVER => [
                "Never",
            ],
            self::REMEMBER_ASK => [
                "Ask the user on login",
            ]
        ];
    }

    /**
     * getRememberLoginAttribute
     */
    public function getRememberLoginAttribute($value)
    {
        if (!$value) {
            return self::REMEMBER_ALWAYS;
        }

        return $value;
    }
}
