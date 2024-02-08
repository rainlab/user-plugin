<?php namespace RainLab\User\Classes;

use Hash;
use RainLab\User\Models\User;
use Illuminate\Auth\SessionGuard as SessionGuardBase;
use InvalidArgumentException;

/**
 * SessionGuard
 *
 * @package october\user
 * @author Alexey Bobkov, Samuel Georges
 */
class SessionGuard extends SessionGuardBase
{
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
        $user->forceFill([$attribute => $password]);
        $user->save();

        return $user;
    }

    /**
     * user gets the currently authenticated user.
     */
    public function user(): ?User
    {
        return parent::user();
    }
}
