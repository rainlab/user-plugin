<?php namespace Plugins\October\User\Facades;

use October\Rain\Support\Facade;

class BackendAuth extends Facade
{
    /**
     * Get the class name this facade is acting on behalf of.
     * @return string
     */
    protected static function getFacadeClass() { return 'Plugins\October\User\Classes\AuthManager'; }
}
