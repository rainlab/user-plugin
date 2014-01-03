<?php namespace Plugins\RainLab\User\Classes;

use October\Rain\Auth\Manager as RainAuthManager;

class AuthManager extends RainAuthManager
{
    protected static $instance;

    protected $userModel = 'Plugins\RainLab\User\Models\User';

    // protected $groupModel = 'Plugins\RainLab\User\Models\Group';

    protected $throttleModel = 'Plugins\RainLab\User\Models\Throttle';
}