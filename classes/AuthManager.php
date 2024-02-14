<?php namespace RainLab\User\Classes;

use Illuminate\Auth\AuthManager as AuthManagerBase;

/**
 * AuthManager
 *
 * @package rainlab\user
 * @author Alexey Bobkov, Samuel Georges
 */
class AuthManager extends AuthManagerBase
{
    /**
     * createSessionDriver create a session based authentication guard, inheriting
     * parent logic as carbon copy. It is modified to return our own flavor of
     * the SessionGuard class.
     *
     * @param  string  $name
     * @param  array  $config
     * @return \RainLab\User\Classes\SessionGuard
     */
    public function createSessionDriver($name, $config)
    {
        $provider = $this->createUserProvider($config['provider'] ?? null);

        $guard = new SessionGuard(
            $name,
            $provider,
            $this->app['session.store'],
        );

        if (method_exists($guard, 'setCookieJar')) {
            $guard->setCookieJar($this->app['cookie']);
        }

        if (method_exists($guard, 'setDispatcher')) {
            $guard->setDispatcher($this->app['events']);
        }

        if (method_exists($guard, 'setRequest')) {
            $guard->setRequest($this->app->refresh('request', $guard, 'setRequest'));
        }

        if (isset($config['remember'])) {
            $guard->setRememberDuration($config['remember']);
        }

        return $guard;
    }
}
