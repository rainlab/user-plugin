<?php namespace RainLab\User\Classes\ActionManager;

use Auth;
use Request;

/**
 * ActionLogout
 *
 * @package rainlab\user
 * @author Alexey Bobkov, Samuel Georges
 */
trait ActionLogout
{
    /**
     * logout signs out the user, or reverts to the original state when impersonating.
     * Returns a custom event response, or null.
     */
    public function logout()
    {
        $user = Auth::user();

        if (Auth::isImpersonator()) {
            Auth::stopImpersonate();
        }
        else {
            Auth::logout();

            if (Request::hasSession()) {
                Request::session()->invalidate();
                Request::session()->regenerateToken();
            }
        }

        if ($user) {
            /**
             * @event rainlab.user.logout
             * Provides custom response logic for logging out a user.
             *
             * Example usage:
             *
             *     Event::listen('rainlab.user.logout', function ($component, $user) {
             *         // Fire logic
             *     });
             *
             * Or
             *
             *     $component->bindEvent('user.logout', function ($user) {
             *         // Fire logic
             *     });
             *
             */
            if ($event = $this->fireSystemEvent('rainlab.user.logout', [$user])) {
                return $event;
            }
        }
    }
}
