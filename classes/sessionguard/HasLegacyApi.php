<?php namespace RainLab\User\Classes\SessionGuard;

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
     * @deprecated use retrieveByCredentials(['login' => $login])
     */
    public function findUserByLogin($login)
    {
        return $this->provider->retrieveByCredentials(['login' => $login]);
    }

    /**
     * @deprecated use retrieveByCredentials
     */
    public function findUserByCredentials(array $credentials)
    {
        return $this->provider->retrieveByCredentials($credentials);
    }
}
