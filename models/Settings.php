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
    }
}