<?php namespace RainLab\User\Components;

use Auth;
use Redirect;
use Cms\Classes\ComponentBase;
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
     * Log out the user
     *
     * Usage:
     *   <a data-request="user::onLogout">Sign out</a>
     */
    public function onLogout()
    {
        Auth::logout();
        return Redirect::to('/');
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