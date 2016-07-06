<?php namespace RainLab\User\Components;

use RainLab\User\Components\Account;

class Login extends Account
{
    public function componentDetails()
    {
        return [
            'name'        => 'rainlab.user::lang.components.login.title',
            'description' => 'rainlab.user::lang.components.login.description'
        ];
    }

}
