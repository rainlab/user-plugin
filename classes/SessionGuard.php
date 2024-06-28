<?php namespace RainLab\User\Classes;

use Hash;
use RainLab\User\Models\User;
use Illuminate\Auth\SessionGuard as SessionGuardBase;
use Illuminate\Contracts\Auth\Authenticatable;
use InvalidArgumentException;
use ValidationException;

/**
 * SessionGuard
 *
 * @package rainlab\user
 * @author Alexey Bobkov, Samuel Georges
 */
class SessionGuard extends SessionGuardBase
{
    use \RainLab\User\Classes\SessionGuard\HasBearerToken;
    use \RainLab\User\Classes\SessionGuard\HasPersistence;
    use \RainLab\User\Classes\SessionGuard\HasImpersonation;
    use \RainLab\User\Classes\SessionGuard\HasLegacyApi;

    /**
     * rehashUserPassword for the current user, overrides parent logic
     * to remove the second hash since it is covered by Hashable during
     * the Auth::logoutOtherDevices method call
     *
     * @param  string  $password
     * @param  string  $attribute
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    protected function rehashUserPassword($password, $attribute)
    {
        $user = $this->user();

        if (!Hash::check($password, $user->{$attribute})) {
            throw new InvalidArgumentException('The given password does not match the current password.');
        }

        // Hashed by Hashable trait
        $user->{$attribute} = $password;
        $user->save(['force' => true]);

        return $user;
    }

    /**
     * login user to the application.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  bool  $remember
     */
    public function login(Authenticatable $user, $remember = false)
    {
        if ($user->is_banned) {
            throw new ValidationException(['password' => __("Your account is locked. Please contact the site administrator.")]);
        }

        $this->preventConcurrentSessions($user);

        parent::login($user, $remember);
    }

    /**
     * Update the session with the given ID.
     *
     * @param  string  $id
     * @return void
     */
    protected function updateSession($id)
    {
        if ($this->viaRemember && $this->user) {
            $this->updatePersistSession($this->user);
        }

        parent::updateSession($id);
    }

    /**
     * loginQuietly logs a user into the application without firing the Login event.
     */
    public function loginQuietly(Authenticatable $user)
    {
        $this->updatePersistSession($user);

        $this->updateSession($user->getAuthIdentifier());

        $this->setUser($user);
    }

    /**
     * user gets the currently authenticated user.
     */
    public function user(): ?User
    {
        $user = parent::user();

        if (!$user) {
            return null;
        }

        if ($user->is_banned) {
            return null;
        }

        if (!$this->viaBearerToken && !$this->hasValidPersistCode($user)) {
            return null;
        }

        return $user;
    }

    /**
     * logoutQuietly logs out the user without updating remember_token and
     * without firing the Logout event.
     */
    public function logoutQuietly()
    {
        $this->clearUserDataFromStorage();

        $this->user = null;

        $this->loggedOut = true;
    }

    /**
     * clearUserDataFromStorage removes the user data from the session and cookies.
     */
    protected function clearUserDataFromStorage()
    {
        $this->session->remove($this->getPersistCodeName());

        parent::clearUserDataFromStorage();
    }

    /**
     * getName as a unique identifier for the auth session value.
     */
    public function getName()
    {
        return 'user_login';
    }

    /**
     * getRecallerName of the cookie used to store the "recaller".
     */
    public function getRecallerName()
    {
        return 'user_auth';
    }
}
