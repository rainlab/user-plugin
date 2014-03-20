<?php namespace RainLab\User\Components;

use Auth;
use Redirect;
use Cms\Classes\ComponentBase;

class Security extends ComponentBase
{

    const ALLOW_ALL = 'all';
    const ALLOW_GUEST = 'guest';
    const ALLOW_USER = 'user';

    public function componentDetails()
    {
        return [
            'name' => 'Security',
            'description' => 'Restricts page access for users'
        ];
    }

    public function defineProperties()
    {
        return [
            'allow' => [
                'title' => 'Allow only',
                'description' => 'Who is allowed to access this page.',
                'type' => 'string' // @todo Dropdown: all, guest, user
            ],
            'redirect' => [
                'title' => 'Redirect to',
                'description' => 'Page name to redirect if access is denied.',
                'type' => 'string'
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

}