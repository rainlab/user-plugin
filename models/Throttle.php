<?php namespace RainLab\User\Models;

use October\Rain\Auth\Models\Throttle as ThrottleBase;

class Throttle extends ThrottleBase
{
    /**
     * @var string The database table used by the model.
     */
    protected $table = 'user_throttle';

    /**
     * @var array Relations
     */
    public $belongsTo = [
        'user' => ['RainLab\User\Models\User']
    ];

    /**
     * Check user throttle status.
     * @return bool
     * @throws AuthException
     */
    public function check()
    {
        if ($this->is_banned) {
            throw new AuthException(sprintf(Lang::get('rainlab.user::lang.login.user_banned'), $this->user->getLogin()));
        }

        if ($this->checkSuspended()) {
            throw new AuthException(sprintf(Lang::get('rainlab.user::lang.login.user_suspended'), $this->user->getLogin()));
        }

        return true;
    }
}
