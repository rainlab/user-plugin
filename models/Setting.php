<?php namespace RainLab\User\Models;

use Config;
use RainLab\User\Classes\PasswordRule;
use Backend\Models\UserGroup as AdminGroup;
use System\Models\SettingModel;

/**
 * Setting configuration
 *
 * @property string login_attribute
 * @property bool block_persistence
 * @property bool allow_registration
 * @property int password_min_length
 * @property bool password_require_mixed_case
 * @property bool password_require_uncompromised
 * @property bool password_require_number
 * @property bool password_require_symbol
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
    const ACTIVATE_USER = 'user';
    const ACTIVATE_ADMIN = 'admin';

    const LOGIN_EMAIL = 'email';
    const LOGIN_USERNAME = 'username';

    /**
     * @var string settingsCode is a unique code for this object
     */
    public $settingsCode = 'user_settings';

    /**
     * @var mixed settingsFields definition file
     */
    public $settingsFields = 'fields.yaml';

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
        $this->block_persistence = Config::get('rainlab.user::block_persistence', false);
        $this->allow_registration = Config::get('rainlab.user::allow_registration', true);
        $this->login_attribute = Config::get('rainlab.user::login_attribute', self::LOGIN_EMAIL);

        $this->password_min_length = Config::get('rainlab.user::password_policy.min_length', 8);
        $this->password_require_mixed_case = Config::get('rainlab.user::password_policy.require_mixed_case', false);
        $this->password_require_uncompromised = Config::get('rainlab.user::password_policy.require_uncompromised', false);
        $this->password_require_number = Config::get('rainlab.user::password_policy.require_number', true);
        $this->password_require_symbol = Config::get('rainlab.user::password_policy.require_symbol', false);

        $this->user_message_template = 'user:welcome_email';
        $this->system_message_template = 'user:new_user_internal';
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
     * makePasswordRule
     */
    public static function makePasswordRule(): PasswordRule
    {
        $setting = static::instance();
        $rule = PasswordRule::default();

        if ($setting->password_min_length) {
            $rule->length($setting->password_min_length);
        }

        if ($setting->password_require_mixed_case) {
            $rule->mixedCase();
        }

        if ($setting->password_require_uncompromised) {
            $rule->uncompromised();
        }

        if ($setting->password_require_number) {
            $rule->numbers();
        }

        if ($setting->password_require_symbol) {
            $rule->symbols();
        }

        return $rule;
    }
}
