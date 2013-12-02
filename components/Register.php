<?php namespace Plugins\October\User\Components;

use Modules\Cms\Classes\ComponentBase;

class Register extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Registration',
            'description' => 'Allows user registration.'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onRegister()
    {
    }

}