<?php namespace RainLab\User\Components;

use Auth;
use Redirect;
use Request;
use Cms\Classes\ComponentBase;
use October\Rain\Support\ValidationException;

class User extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name'        => 'User Provider',
            'description' => 'General user-based features.'
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
     *
     * With the optional redirect parameter:
     *   <a data-request="user::onLogout" data-request-data="redirect: '/good-bye'">Sign out</a>
     *
     */
    public function onLogout()
    {
        Auth::logout();
        $url = post('redirect', Request::fullUrl());
        return Redirect::to($url);
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