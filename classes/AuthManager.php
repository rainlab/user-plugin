<?php namespace RainLab\User\Classes;

use October\Rain\Auth\Manager as RainAuthManager;
use RainLab\User\Models\Settings as UserSettings;

class AuthManager extends RainAuthManager
{
    protected static $instance;

    protected $sessionKey = 'user_auth';

    protected $userModel = 'RainLab\User\Models\User';

    // protected $groupModel = 'RainLab\User\Models\Group';

    protected $throttleModel = 'RainLab\User\Models\Throttle';

    public function init()
    {
        $this->useThrottle = UserSettings::get('use_throttle', $this->useThrottle);
        $this->requireActivation = UserSettings::get('require_activation', $this->requireActivation);
        parent::init();
    }
}