<?php namespace RainLab\User\Models;

use Model;

class Settings extends Model
{
    public $implement = ['System.Behaviors.SettingsModel'];

    public $settingsCode = 'user_settings';
    public $settingsFields = 'fields.yaml';

    public function initSettingsData()
    {
        $this->require_activation = true;
        $this->auto_activation = true;
        $this->use_throttle = true;
        $this->use_throttle = true;
        $this->default_country = 1;
        $this->default_state = 1;
    }

    public function getDefaultCountryOptions()
    {
        return Country::getNameList();
    }

    public function getDefaultStateOptions()
    {
        return State::getNameList($this->default_country);
    }
}