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
     * newModelQuery adjusts the lookup query to exclude guest accounts
     */
    protected function newModelQuery($model = null)
    {
        return parent::newModelQuery($model)->applyRegistered();
    }
}
