<?php namespace Plugins\October\User\Facades;

use October\Rain\Support\Facade;

class Auth extends Facade
{
    /**
     * Get the class name this facade is acting on behalf of.
     * @return string
     */
    protected static function getFacadeAccessor() { return 'Plugins\October\User\Classes\AuthManager'; }
}
