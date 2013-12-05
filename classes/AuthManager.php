<?php namespace Plugins\October\User\Classes;

use October\Rain\Auth\Manager as RainAuthManager;

class AuthManager extends RainAuthManager
{
    protected static $instance;

    protected $userModel = 'Plugins\October\User\Models\User';

    // protected $groupModel = 'Plugins\October\User\Models\Group';

    // protected $throttleModel = 'Plugins\October\User\Models\Throttle';
}