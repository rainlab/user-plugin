<?php namespace RainLab\User\Classes\SessionGuard;

use RainLab\User\Helpers\User as UserHelper;

/**
 * HasLegacyApi provides deprecated interface for Auth
 *
 * @package rainlab\user
 * @author Alexey Bobkov, Samuel Georges
 */
trait HasLegacyApi
{
    /**
     * @deprecated use retrieveById
     */
    public function findUserById($id)
    {
        return $this->provider->retrieveById($id);
    }

    /**
     * @deprecated use `getProvider()->retrieveByCredentials(['email' => $email])`
     */
    public function findUserByLogin($login)
    {
        return $this->provider->retrieveByCredentials([UserHelper::username() => $login]);
    }

    /**
     * @deprecated use `getProvider()->retrieveByCredentials`
     */
    public function findUserByCredentials(array $credentials)
    {
        $user = $this->provider->retrieveByCredentials($credentials);
        if (!$user) {
            return null;
        }

        if (
            array_key_exists('password', $credentials) &&
            !$this->provider->validateCredentials($user, $credentials)
        ) {
            return null;
        }

        return $user;
    }

    /**
     * @deprecated create User model manually
     */
    public function register(array $credentials, $activate = false, $autoLogin = true)
    {
        $user = \RainLab\User\Models\User::create($credentials);

        if ($activate) {
            $user->markEmailAsVerified();
        }

        if ($autoLogin) {
            $this->loginQuietly($user);
        }

        return $user;
    }
}
