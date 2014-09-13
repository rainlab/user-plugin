<?php namespace RainLab\User\Models;

use Lang;
use Model;
use System\Models\MailTemplate;

class Settings extends Model
{
    public $implement = ['System.Behaviors.SettingsModel'];

    public $settingsCode = 'user_settings';
    public $settingsFields = 'fields.yaml';

    const ACTIVATE_AUTO = 'auto';
    const ACTIVATE_USER = 'user';
    const ACTIVATE_ADMIN = 'admin';

    public function initSettingsData()
    {
        $this->require_activation = true;
        $this->activate_mode = self::ACTIVATE_AUTO;
        $this->use_throttle = true;
        $this->default_country = 1;
        $this->default_state = 1;
        $this->welcome_template = 'rainlab.user::mail.welcome';
    }

    public function getDefaultCountryOptions()
    {
        return Country::getNameList();
    }

    public function getDefaultStateOptions()
    {
        return State::getNameList($this->default_country);
    }

    public function getActivateModeOptions()
    {
        return [
            self::ACTIVATE_AUTO => ['rainlab.user::lang.settings.activate_mode_auto', 'rainlab.user::lang.settings.activate_mode_auto_comment'],
            self::ACTIVATE_USER => ['rainlab.user::lang.settings.activate_mode_user', 'rainlab.user::lang.settings.activate_mode_user_comment'],
            self::ACTIVATE_ADMIN => ['rainlab.user::lang.settings.activate_mode_admin', 'rainlab.user::lang.settings.activate_mode_admin_comment'],
        ];
    }

    public function getActivateModeAttribute($value)
    {
        if (!$value)
            return self::ACTIVATE_AUTO;

        return $value;
    }

    public function getWelcomeTemplateOptions()
    {
        return [''=>'- '.Lang::get('rainlab.user::lang.settings.no_mail_template').' -'] + MailTemplate::orderBy('code')->lists('code', 'code');
    }

}