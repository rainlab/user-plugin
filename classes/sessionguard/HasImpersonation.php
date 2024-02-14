<?php namespace RainLab\User\Classes\SessionGuard;

use RainLab\User\Models\User;

/**
 * HasImpersonation
 *
 * @package rainlab\user
 * @author Alexey Bobkov, Samuel Georges
 */
trait HasImpersonation
{
    /**
     * impersonate a user
     */
    public function impersonate(User $user)
    {
    }

    /**
     * getImpersonator returns the underlying user impersonating
     */
    public function getImpersonator(): ?User
    {
        return null;
    }

    /**
     * stopImpersonate stops impersonating a user
     */
    public function stopImpersonate()
    {
    }
}
