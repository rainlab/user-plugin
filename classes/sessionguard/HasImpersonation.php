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
        $oldUser = $this->user();

        /**
         * @event model.auth.beforeImpersonate
         *
         * Example usage:
         *
         *     $model->bindEvent('model.auth.beforeImpersonate', function (\October\Rain\Database\Model|null $oldUser) use (\October\Rain\Database\Model $model) {
         *         traceLog($oldUser->full_name . ' is now impersonating ' . $model->full_name);
         *     });
         *
         */
        $user->fireEvent('model.auth.beforeImpersonate', [$oldUser]);

        // Replace session with impersonated user
        $this->loginQuietly($user);

        // If this is the first time impersonating, capture the original user
        if (!$this->isImpersonator()) {
            $this->session->put($this->getImpersonateName(), $oldUser?->getKey() ?: 'NaN');
        }
    }

    /**
     * stopImpersonate stops impersonating a user
     */
    public function stopImpersonate()
    {
        // Determine current and previous user
        $currentUser = $this->user();
        $oldUser = $this->getImpersonator();

        if ($currentUser) {
            /**
             * @event model.auth.afterImpersonate
             *
             * Example usage:
             *
             *     $model->bindEvent('model.auth.afterImpersonate', function (\October\Rain\Database\Model|null $oldUser) use (\October\Rain\Database\Model $model) {
             *         traceLog($oldUser->full_name . ' has stopped impersonating ' . $model->full_name);
             *     });
             *
             */
            $currentUser->fireEvent('model.auth.afterImpersonate', [$oldUser]);
        }

        // Restore previous user, if possible
        if ($oldUser) {
            $this->loginQuietly($oldUser);
        }
        else {
            $this->logoutQuietly();
        }

        $this->session->remove($this->getImpersonateName());
    }

    /**
     * getImpersonator returns the underlying user impersonating
     */
    public function getImpersonator(): ?User
    {
        if (!$this->session->has($this->getImpersonateName())) {
            return null;
        }

        $oldUserId = $this->session->get($this->getImpersonateName());
        if ((!is_string($oldUserId) && !is_int($oldUserId)) || $oldUserId === 'NaN') {
            return null;
        }

        return $this->provider->retrieveById($oldUserId);
    }

    /**
     * getRealUser gets the "real" user to bypass impersonation.
     */
    public function getRealUser(): ?User
    {
        if ($user = $this->getImpersonator()) {
            return $user;
        }

        return $this->getUser();
    }

    /**
     * isImpersonator checks to see if the current session is being impersonated.
     * @return bool
     */
    public function isImpersonator()
    {
        return $this->session->has($this->getImpersonateName());
    }

    /**
     * getImpersonationName gets the name of the session used to store the impersonator.
     */
    public function getImpersonateName()
    {
        return 'user_impersonate';
    }
}
