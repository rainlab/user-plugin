<?php namespace Plugins\RainLab\User\Components;

use Modules\Cms\Classes\ComponentBase;
use October\Rain\Support\ValidationException;

class User extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Active User',
            'description' => 'Attaches the logged in user.'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

}