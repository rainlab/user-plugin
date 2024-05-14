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
     * @deprecated use retrieveByCredentials(['email' => $email])
     */
    public function findUserByLogin($login)
    {
        return $this->provider->retrieveByCredentials([UserHelper::username() => $login]);
    }

    /**
     * @deprecated use retrieveByCredentials
     */
    public function findUserByCredentials(array $credentials)
    {
        return $this->provider->retrieveByCredentials($credentials);
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
