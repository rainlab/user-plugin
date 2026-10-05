<?php namespace RainLab\User\Classes\SessionGuard;

use Auth;
use RainLab\User\Models\User;
use RainLab\User\Models\Setting as UserSetting;

/**
 * HasPersistence
 *
 * @package rainlab\user
 * @author Alexey Bobkov, Samuel Georges
 */
trait HasPersistence
{
    /**
     * logoutOtherDevicesForcefully is like logoutOtherDevices except it resets the cookie
     */
    public function logoutOtherDevicesForcefully(User $user)
    {
        $validationForced = $user->validationForced;

        $user->validationForced = true;

        $user->setPersistCode($user->generatePersistCode());

        // Expected save() call in here
        $this->cycleRememberToken($user);

        $user->validationForced = $validationForced;
    }

    /**
     * preventConcurrentSessions for the supplied user, if configured
     */
    protected function preventConcurrentSessions(User $user)
    {
        if (UserSetting::get('block_persistence', false)) {
            $this->logoutOtherDevicesForcefully($user);
        }

        $this->updatePersistSession($user);
    }

    /**
     * updatePersistSession
     */
    protected function updatePersistSession(User $user)
    {
        return $this->session->put($this->getPersistCodeName(), $user->getPersistCode());
    }

    /**
     * hasValidPersistCode
     */
    protected function hasValidPersistCode(User $user)
    {
        return $this->session->get($this->getPersistCodeName()) === $user->getPersistCode();
    }

    /**
     * getPersistCodeName gets the name of the session used to store the checksum.
     */
    public function getPersistCodeName()
    {
        return 'user_persist_code';
    }

    /**
     * updatePasswordHashSession stores an HMAC of the user's password hash in the session.
     */
    public function updatePasswordHashSession(User $user): void
    {
        if ($passwordHash = $user->getAuthPassword()) {
            $this->session->put($this->getPasswordHashName(), $this->makePasswordHashValue($passwordHash));
        }
    }

    /**
     * makePasswordHashValue returns the HMAC of a password hash, or the hash itself when the framework has no HMAC support.
     */
    protected function makePasswordHashValue(string $passwordHash): string
    {
        return method_exists($this, 'hashPasswordForCookie')
            ? $this->hashPasswordForCookie($passwordHash)
            : $passwordHash;
    }

    /**
     * hasPasswordHashSession returns true when a password hash is stored in the session.
     */
    public function hasPasswordHashSession(): bool
    {
        return $this->session->has($this->getPasswordHashName());
    }

    /**
     * hasValidPasswordHash checks the session password hash against the user's current password.
     */
    public function hasValidPasswordHash(User $user): bool
    {
        return $this->validatePasswordHash(
            $user->getAuthPassword(),
            $this->session->get($this->getPasswordHashName())
        );
    }

    /**
     * validatePasswordHash checks a stored value against the HMAC or raw form of a password hash.
     */
    public function validatePasswordHash($passwordHash, $storedValue): bool
    {
        if (!is_string($passwordHash) || !is_string($storedValue)) {
            return false;
        }

        return hash_equals($this->makePasswordHashValue($passwordHash), $storedValue)
            || hash_equals($passwordHash, $storedValue);
    }

    /**
     * getPasswordHashName gets the name of the session used to store the password hash.
     */
    public function getPasswordHashName()
    {
        return 'password_hash_'.$this->name;
    }
}
