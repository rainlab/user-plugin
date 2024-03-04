<?php namespace RainLab\User\Models;

use Config;
use Backend\Models\UserGroup as AdminGroup;
use System\Models\SettingModel;

/**
 * Setting configuration
 *
 * @property string remember_login
 * @property string login_attribute
 * @property bool block_persistence
 * @property bool require_activation
 * @property string activate_mode
 * @property bool allow_registration
 * @property int min_password_length
 * @property bool require_mixed_case
 * @property bool require_uncompromised
 * @property bool require_number
 * @property bool require_symbol
 * @property bool notify_user
 * @property string user_message_template
 * @property bool notify_system
 * @property string system_message_template
 * @property \Backend\Models\UserGroup|null admin_group
 *
 * @package rainlab\user
 * @author Alexey Bobkov, Samuel Georges
 */
class Setting extends SettingModel
{
    /**
     * @var string settingsCode is a unique code for this object
     */
    public $settingsCode = 'user_settings';

    /**
     * @var mixed settingsFields definition file
     */
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
     * @var array belongsTo relations
     */
    public $belongsTo = [
        'admin_group' => AdminGroup::class,
    ];

    /**
     * initSettingsData
     */
    public function initSettingsData()
    {
        $this->require_activation = Config::get('rainlab.user::require_activation', false);
        $this->activate_mode = Config::get('rainlab.user::activate_mode', self::ACTIVATE_AUTO);
        $this->block_persistence = Config::get('rainlab.user::block_persistence', false);
        $this->allow_registration = Config::get('rainlab.user::allow_registration', true);
        $this->login_attribute = Config::get('rainlab.user::login_attribute', self::LOGIN_EMAIL);
        $this->remember_login = Config::get('rainlab.user::remember_login', self::REMEMBER_ALWAYS);
        $this->user_message_template = 'user:welcome_email';
        $this->system_message_template = 'user:new_user_internal';
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
