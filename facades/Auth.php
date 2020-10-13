<?php namespace RainLab\User\Facades;

use October\Rain\Support\Facade;

/**
 * @see \RainLab\User\Classes\AuthManager
 */
class Auth extends Facade
{
    /**
     * Get the registered name of the component.
     * @return string
     */
    protected static function getFacadeAccessor() { return 'user.auth'; }
}
