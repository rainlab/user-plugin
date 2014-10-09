<?php namespace RainLab\User\Components;

use Auth;
use Request;
use Redirect;
use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use October\Rain\Support\ValidationException;

class Session extends ComponentBase
{

    const ALLOW_ALL = 'all';
    const ALLOW_GUEST = 'guest';
    const ALLOW_USER = 'user';

    public function componentDetails()
    {
        return [
            'name'        => e(trans('rainlab.user::lang.session.session')),
            'description' => e(trans('rainlab.user::lang.session.session_desc'))
        ];
    }

    public function defineProperties()
    {
        return [
            'security' => [
                'title'       => e(trans('rainlab.user::lang.session.security_title')),
                'description' => e(trans('rainlab.user::lang.session.security_desc')),
                'type'        => 'dropdown',
                'default'     => 'all',
                'options'     => ['all' => e(trans('rainlab.user::lang.session.all')),
                                  'user' => e(trans('rainlab.user::lang.session.users')),
                                  'guest' => e(trans('rainlab.user::lang.session.guests'))]
            ],
            'redirect' => [
                'title'       => e(trans('rainlab.user::lang.session.redirect_title')),
                'description' => e(trans('rainlab.user::lang.session.redirect_desc')),
                'type'        => 'dropdown',
                'default'     => ''
            ]
        ];
    }

    public function getRedirectOptions()
    {
        return [''=>'- none -'] + Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    /**
     * Executed when this component is bound to a page or layout.
     */
    public function onRun()
    {
        $redirectUrl = $this->controller->pageUrl($this->property('redirect'));
        $allowedGroup = $this->property('security', self::ALLOW_ALL);
        $isAuthenticated = Auth::check();

        if (!$isAuthenticated && $allowedGroup == self::ALLOW_USER)
            return Redirect::intended($redirectUrl);

        elseif ($isAuthenticated && $allowedGroup == self::ALLOW_GUEST)
            return Redirect::intended($redirectUrl);

        $this->page['user'] = $this->user();
    }

    /**
     * Log out the user
     *
     * Usage:
     *   <a data-request="onLogout">Sign out</a>
     *
     * With the optional redirect parameter:
     *   <a data-request="onLogout" data-request-data="redirect: '/good-bye'">Sign out</a>
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
    public function user()
    {
        if (!Auth::check())
            return null;

        return Auth::getUser();
    }

}