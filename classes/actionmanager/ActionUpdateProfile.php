<?php namespace RainLab\User\Classes\ActionManager;

use RainLab\User\Models\UserLog;
use RainLab\User\Models\UserPreference;
use ForbiddenException;

/**
 * ActionUpdateProfile
 *
 * @package rainlab\user
 * @author Alexey Bobkov, Samuel Georges
 */
trait ActionUpdateProfile
{
    /**
     * updateProfile updates the profile information of the authenticated user. Supported options:
     *
     * - avatar: an uploaded file to use as the user avatar.
     * - removeAvatar: set to true to remove the existing avatar.
     *
     * Returns a custom event response, or null.
     */
    public function updateProfile(array $input, array $options = [])
    {
        $user = $this->user();
        if (!$user) {
            throw new ForbiddenException;
        }

        // Password update requires old password, use the changePassword action instead
        $input = array_except($input, ['password', 'remove_avatar']);

        /**
         * @event rainlab.user.beforeUpdate
         * Provides custom logic for updating a user profile.
         *
         * Example usage:
         *
         *     Event::listen('rainlab.user.beforeUpdate', function ($component, $user, &$input) {
         *         $input['some_field'] = post('to_save');
         *     });
         *
         * Or
         *
         *     $component->bindEvent('user.beforeUpdate', function ($user, &$input) {
         *         $input['some_field'] = post('to_save');
         *     });
         *
         */
        $this->fireSystemEvent('rainlab.user.beforeUpdate', [$user, &$input]);

        // Avatar upload
        if ($avatarFile = array_get($options, 'avatar')) {
            $user->avatar = $avatarFile;
        }
        elseif (array_get($options, 'removeAvatar')) {
            $user->avatar = null;
        }

        // Preference upload
        if (($preferences = array_get($input, 'Preference')) && is_array($preferences)) {
            UserPreference::setPreferencesSafe($user->id, $preferences);
        }

        // Email changed
        if (isset($input['email']) && $user->email !== trim($input['email'])) {
            $user->forceFill(['activated_at' => null]);

            UserLog::createRecord($user->getKey(), UserLog::TYPE_SET_EMAIL, [
                'user_full_name' => $user->full_name,
                'old_value' => $user->email,
                'new_value' => $input['email']
            ]);
        }

        $user->fill($input);
        $user->save();

        /**
         * @event rainlab.user.update
         * Provides custom response logic after a user profile is updated.
         *
         * Example usage:
         *
         *     Event::listen('rainlab.user.update', function ($component, $user, $input) {
         *         // ...
         *     });
         *
         * Or
         *
         *     $component->bindEvent('user.update', function ($user, $input) {
         *         // ...
         *     });
         *
         */
        if ($event = $this->fireSystemEvent('rainlab.user.update', [$user, $input])) {
            return $event;
        }
    }
}
