<?php namespace RainLab\User\Classes;

use Hash;
use RainLab\User\Models\User;
use Illuminate\Auth\SessionGuard as SessionGuardBase;
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
    use \RainLab\User\Classes\SessionGuard\HasImpersonation;

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
     * login user to the application.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  bool  $remember
     */
    public function login($user, $remember = false)
    {
        if ($user->is_banned) {
            throw new ValidationException(['password' => __("Your account is locked. Please contact the site administrator.")]);
        }

        return parent::login($user, $remember);
    }

    /**
     * user gets the currently authenticated user.
     */
    public function user(): ?User
    {
        $user = parent::user();

        if ($user && $user->is_banned) {
            return null;
        }

        return $user;
    }
}
