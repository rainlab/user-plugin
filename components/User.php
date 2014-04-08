<?php namespace RainLab\User\Components;

use Auth;
use Redirect;
use Request;
use Cms\Classes\ComponentBase;
use October\Rain\Support\ValidationException;

class User extends ComponentBase
{

    const ALLOW_ALL = 'all';
    const ALLOW_GUEST = 'guest';
    const ALLOW_USER = 'user';

    public function componentDetails()
    {
        return [
            'name'        => 'User',
            'description' => 'Adds the user object to a page and can restrict page access.'
        ];
    }

    public function defineProperties()
    {
        return [
            'security' => [
                'title'       => 'Allow only',
                'description' => 'Who is allowed to access this page.',
                'type'        => 'string', // @todo Dropdown: all, guest, user
                'default'     => 'all'
            ],
            'redirect' => [
                'title'       => 'Redirect to',
                'description' => 'Page name to redirect if access is denied.',
                'type'        => 'string'
            ]
        ];
    }

    /**
     * Executed when this component is bound to a page or layout.
     */
    public function onRun()
    {
        $redirectUrl = $this->controller->pageUrl($this->property('redirect'));
        $allowedGroup = $this->property('allow', self::ALLOW_ALL);
        $isAuthenticated = Auth::check();

        if (!$isAuthenticated && $allowedGroup == self::ALLOW_USER)
            return Redirect::intended($redirectUrl);

        elseif ($isAuthenticated && $allowedGroup == self::ALLOW_GUEST)
            return Redirect::intended($redirectUrl);
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