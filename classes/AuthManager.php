<?php namespace RainLab\User\Classes;

use October\Rain\Auth\Manager as RainAuthManager;

class AuthManager extends RainAuthManager
{
    protected static $instance;

    protected $sessionKey = 'user_auth';

    protected $userModel = 'RainLab\User\Models\User';

    // protected $groupModel = 'RainLab\User\Models\Group';

    protected $throttleModel = 'RainLab\User\Models\Throttle';
}