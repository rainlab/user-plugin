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
}
