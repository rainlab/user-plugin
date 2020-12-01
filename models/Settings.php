<?php namespace RainLab\User\Models;

use Model;

class Settings extends Model
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

    public function initSettingsData()
    {
        $this->require_activation = config('rainlab.user::requireActivation', true);
        $this->activate_mode = config('rainlab.user::activateMode', self::ACTIVATE_AUTO);
        $this->use_throttle = config('rainlab.user::useThrottle', true);
        $this->block_persistence = config('rainlab.user::blockPersistence', false);
        $this->allow_registration = config('rainlab.user::allowRegistration', true);
        $this->login_attribute = config('rainlab.user::loginAttribute', self::LOGIN_EMAIL);
        $this->remember_login = config('rainlab.user::rememberLogin', self::REMEMBER_ALWAYS);
        $this->use_register_throttle = config('rainlab.user::useRegisterThrottle', true);
    }

    public function getActivateModeOptions()
    {
        return [
            self::ACTIVATE_AUTO => [
                'rainlab.user::lang.settings.activate_mode_auto',
                'rainlab.user::lang.settings.activate_mode_auto_comment'
            ],
            self::ACTIVATE_USER => [
                'rainlab.user::lang.settings.activate_mode_user',
                'rainlab.user::lang.settings.activate_mode_user_comment'
            ],
            self::ACTIVATE_ADMIN => [
                'rainlab.user::lang.settings.activate_mode_admin',
                'rainlab.user::lang.settings.activate_mode_admin_comment'
            ]
        ];
    }

    public function getActivateModeAttribute($value)
    {
        if (!$value) {
            return self::ACTIVATE_AUTO;
        }

        return $value;
    }

    public function getLoginAttributeOptions()
    {
        return [
            self::LOGIN_EMAIL => ['rainlab.user::lang.login.attribute_email'],
            self::LOGIN_USERNAME => ['rainlab.user::lang.login.attribute_username']
        ];
    }

    public function getRememberLoginOptions()
    {
        return [
            self::REMEMBER_ALWAYS => [
                'rainlab.user::lang.settings.remember_always',
            ],
            self::REMEMBER_NEVER => [
                'rainlab.user::lang.settings.remember_never',
            ],
            self::REMEMBER_ASK => [
                'rainlab.user::lang.settings.remember_ask',
            ]
        ];
    }

    public function getRememberLoginAttribute($value)
    {
        if (!$value) {
            return self::REMEMBER_ALWAYS;
        }

        return $value;
    }
}
