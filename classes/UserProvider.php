<?php namespace RainLab\User\Classes;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * UserProvider
 *
 * @package rainlab\user
 * @author Alexey Bobkov, Samuel Georges
 */
class UserProvider extends EloquentUserProvider
{
    /**
     * rehashPasswordIfRequired passes the plain password to the model since the Hashable trait hashes it on save.
     */
    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false)
    {
        if (!$this->hasher->needsRehash($user->getAuthPassword()) && !$force) {
            return;
        }

        $user->forceFill([
            $user->getAuthPasswordName() => $credentials['password'],
        ])->forceSave();
    }

    /**
     * updateRememberToken saves the token without validating the user, since stored data that
     * fails the model rules, such as a missing avatar file, should not stop signing in or out.
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        $user->setRememberToken($token);

        $timestamps = $user->timestamps;

        $user->timestamps = false;

        $user->forceSave();

        $user->timestamps = $timestamps;
    }

    /**
     * newModelQuery adjusts the lookup query to exclude guest accounts
     */
    protected function newModelQuery($model = null)
    {
        return parent::newModelQuery($model)->applyRegistered();
    }
}
