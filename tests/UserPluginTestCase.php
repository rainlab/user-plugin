<?php namespace RainLab\User\Tests;

use App;
use PluginTestCase;
use Illuminate\Foundation\AliasLoader;
use RainLab\User\Models\Setting;

/**
 * UserPluginTestCase
 */
abstract class UserPluginTestCase extends PluginTestCase
{
    /**
     * setUp test case
     */
    public function setUp(): void
    {
        parent::setUp();

        // Reset any modified settings
        Setting::resetDefault();

        // Log out after each test
        \RainLab\User\Classes\AuthManager::instance()->logout();

        // Register the auth facade
        $alias = AliasLoader::getInstance();
        $alias->alias('Auth', \RainLab\User\Facades\Auth::class);

        App::singleton('user.auth', function () {
            return \RainLab\User\Classes\AuthManager::instance();
        });
    }
}
