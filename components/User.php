<?php namespace Plugins\RainLab\User\Components;

use Auth;
use Modules\Cms\Classes\ComponentBase;
use October\Rain\Support\ValidationException;

class User extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'User Provider',
            'description' => 'Provides user-based utilities.'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    /**
     * Returns the logged in user, if available
     */
    public function active()
    {
        if (!Auth::check())
            return null;

        return Auth::getUser();
    }

}