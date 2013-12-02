<?php namespace Plugins\October\User\Components;

use Modules\Cms\Classes\ComponentBase;

class Signin extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Sign in',
            'description' => 'Allows users to sign in.'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onSignin()
    {
    }

}