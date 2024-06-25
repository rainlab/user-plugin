<?php namespace RainLab\User\Components;

use Cms;
use Auth;
use Flash;
use RainLab\User\Models\User;
use RainLab\User\Models\UserLog;
use RainLab\User\Models\UserPreference;
use Cms\Classes\ComponentBase;
use ApplicationException;
use ValidationException;
use ForbiddenException;

/**
 * Account component
 *
 * Allows users to update their account. They can also deactivate their account,
 * enable two-factor and resend the account verification email.
 *
 * @package rainlab\user
 * @author Alexey Bobkov, Samuel Georges
 */
class Account extends ComponentBase
{
    use \RainLab\User\Traits\ConfirmsPassword;
    use \RainLab\User\Components\Account\ActionTwoFactor;
    use \RainLab\User\Components\Account\ActionDeleteUser;
    use \RainLab\User\Components\Account\ActionVerifyEmail;
    use \RainLab\User\Components\Account\ActionBrowserSessions;

    /**
     * componentDetails
     */
    public function componentDetails()
    {
        return [
            'name' => "Account",
            'description' => "User management form for updating profile and security details."
        ];
    }

    /**
     * defineProperties
     */
    public function defineProperties()
    {
        return [
            'isDefault' => [
                'title' => 'Default View',
                'type' => 'checkbox',
                'description' => 'Use this page as the default entry point when verifying the email address.',
                'showExternalParam' => false
            ],
        ];
    }

    /**
     * onRun
     */
    public function onRun()
    {
        if ($redirect = $this->checkVerifyEmailRedirect()) {
            return $redirect;
        }
    }

    /**
     * onUpdateProfile information
     */
    public function onUpdateProfile()
    {
        $user = $this->user();
        if (!$user) {
            throw new ForbiddenException;
        }

        // Password update requires old password, use RainLab\User\Components\ResetPassword instead
        $input = array_except((array) post(), ['password', 'remove_avatar']);

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
        if ($avatarFile = files('avatar')) {
            $user->avatar = $avatarFile;
        }
        elseif (post('remove_avatar')) {
            $user->avatar = null;
        }

        // Preference upload
        if (($preferences = post('Preference')) && is_array($preferences)) {
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
         * Provides custom logic when a login attempt has been rate limited.
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

        if ($flash = Cms::flashFromPost(__("Your profile has been updated."))) {
            Flash::success($flash);
        }

        if ($redirect = Cms::redirectFromPost()) {
            return $redirect;
        }
    }

    /**
     * onVerifyEmail sends the verification email to the user
     */
    public function onVerifyEmail()
    {
        $this->actionVerifyEmail();

        if ($flash = Cms::flashFromPost(__("Please check your email for instructions."))) {
            Flash::success($flash);
        }

        $this->page['showLinkSent'] = true;
    }

    /**
     * onConfirmEmail is used
     */
    protected function onConfirmEmail()
    {
        try {
            $this->actionConfirmEmail(post('verify'));
        }
        catch (ApplicationException $ex) {
            throw new ValidationException([
                'verify' => $ex->getMessage(),
            ]);
        }

        $this->page['showSuccess'] = true;
    }

    /**
     * onEnableTwoFactor
     */
    public function onEnableTwoFactor()
    {
        if ($result = $this->checkConfirmedPassword()) {
            return $result;
        }

        $this->actionEnableTwoFactor();

        $this->page['showConfirmation'] = true;
    }

    /**
     * onConfirmTwoFactor
     */
    public function onConfirmTwoFactor()
    {
        $this->actionConfirmTwoFactor();

        $this->page['showRecoveryCodes'] = true;
    }

    /**
     * onShowTwoFactorRecoveryCodes
     */
    public function onShowTwoFactorRecoveryCodes()
    {
        if ($result = $this->checkConfirmedPassword()) {
            return $result;
        }

        $this->page['showRecoveryCodes'] = true;
    }

    /**
     * onRegenerateTwoFactorRecoveryCodes
     */
    public function onRegenerateTwoFactorRecoveryCodes()
    {
        $this->actionRegenerateTwoFactorRecoveryCodes();

        $this->page['showRecoveryCodes'] = true;
    }

    /**
     * onDisableTwoFactor
     */
    public function onDisableTwoFactor()
    {
        if ($result = $this->checkConfirmedPassword()) {
            return $result;
        }

        $this->actionDisableTwoFactor();
    }

    /**
     * onDeleteOtherSessions from storage.
     */
    protected function onDeleteOtherSessions()
    {
        $this->actionDeleteOtherSessions();

        if ($flash = Cms::flashFromPost(__("Your other browser sessions have been logged out."))) {
            Flash::success($flash);
        }

        if ($redirect = Cms::redirectFromPost()) {
            return $redirect;
        }
    }

    /**
     * onDeleteUser
     */
    protected function onDeleteUser()
    {
        $this->actionDeleteUser();

        if ($flash = Cms::flashFromPost(__("Your account has been removed from our system."))) {
            Flash::success($flash);
        }

        if ($redirect = Cms::redirectFromPost()) {
            return $redirect;
        }
    }

    /**
     * user returns the logged in user
     */
    public function user(): ?User
    {
        return Auth::user();
    }

    /**
     * sessions returns browser sessions for the user
     */
    public function sessions(): array
    {
        return $this->fetchSessions();
    }

    /**
     * twoFactorEnabled returns true if the user has two factor enabled
     */
    public function twoFactorEnabled(): bool
    {
        return $this->fetchTwoFactorEnabled();
    }

    /**
     * twoFactorRecoveryCodes returns an array of recovery codes, if available
     */
    public function twoFactorRecoveryCodes(): array
    {
        return $this->fetchTwoFactorRecoveryCodes();
    }
}
