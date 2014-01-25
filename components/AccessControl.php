<?php namespace RainLab\User\Components;

use Cms\Classes\ComponentBase;

class AccessControl extends ComponentBase
{

    const ALLOW_ALL = 'all';
    const ALLOW_GUEST = 'guest';
    const ALLOW_USER = 'user';

    public function componentDetails()
    {
        return [
            'name' => 'Access Control',
            'description' => 'Restricts page access for users'
        ];
    }

    public function defineProperties()
    {
        return [
            'allow-only' => [
                'title' => 'Allow only',
                'description' => 'Who is allowed to access this page.',
                'type' => 'string' // @todo Dropdown: all, guest, user
            ],
            'redirect-to' => [
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
        $allowedGroup = $this->property('allow-only', self::ALLOW_ALL);
    }

}