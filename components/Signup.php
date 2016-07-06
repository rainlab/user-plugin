<?php namespace RainLab\User\Components;

use Lang;
use RainLab\User\Components\Account;
use RainLab\User\Models\Settings as UserSettings;

class Signup extends Account
{
    private $config, $lang;

    public function __construct($cmsObject = null, $properties =[])
    {
        parent::__construct($cmsObject, $properties);
        $this->config = [
            'rules_active' => UserSettings::get('rules_active', false),
            'rules_text' => UserSettings::get('rules_text', ''),
        ];

        $this->lang = Lang::getLocale();;
    }

    public function onRun()
    {
        parent::onRun();

        $this->page['config'] =  $this->config;
    }

    public function componentDetails()
    {
        return [
            'name'        => 'rainlab.user::lang.components.signup.title',
            'description' => 'rainlab.user::lang.components.signup.description'
        ];
    }

    public function defineProperties()
    {
        return [
            'redirect' => [
                'title'       => 'rainlab.user::lang.account.redirect_to',
                'description' => 'rainlab.user::lang.account.redirect_to_desc',
                'type'        => 'dropdown',
                'default'     => ''
            ],
            'paramCode' => [
                'title'       => 'rainlab.user::lang.account.code_param',
                'description' => 'rainlab.user::lang.account.code_param_desc',
                'type'        => 'string',
                'default'     => 'code'
            ]
        ];
    }
}
