<?php namespace RainLab\User\Components;

use Cms;
use Date;
use Auth;
use Flash;
use Request;
use Redirect;
use RainLab\User\Models\User;
use RainLab\User\Models\UserPreference;
use Cms\Classes\ComponentBase;
use ApplicationException;
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
    use \RainLab\User\Components\Account\ActionBrowserSessions;

    /**
     * componentDetails
     */
    public function componentDetails()
    {
        return [
            'name' => "Account",
            'description' => "User management form."
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
                'description' => 'Used as default entry point when confirming an email address.',
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
        $data = array_except((array) post(), ['password', 'remove_avatar']);

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

        $user->fill($data);
        $user->save();

        if ($event = $this->fireSystemEvent('user.profile.update', [$user])) {
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
     * onVerifyEmail
     */
    public function onVerifyEmail()
    {
        $user = $this->user();
        if (!$user) {
            throw new ForbiddenException;
        }

        $limiter = $this->makeVerifyRateLimiter();

        if ($limiter->tooManyAttempts(1)) {
            $seconds = $limiter->availableIn();

            throw new ApplicationException(__("Too many verification attempts. Please try again in :seconds seconds.", [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]));
        }

        $limiter->increment();

        $user->sendEmailVerificationNotification();

        if ($flash = Cms::flashFromPost(__("Please check your email for instructions."))) {
            Flash::success($flash);
        }

        $this->page['showLinkSent'] = true;
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
     * checkVerifyEmailRedirect
     */
    protected function checkVerifyEmailRedirect()
    {
        if (!get('verify') || !get('id') || !Request::hasValidSignature()) {
            return;
        }

        $user = $this->user();
        if (!$user) {
            return;
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        Flash::success(__("Thank you for verifying your email."));

        return Redirect::to(Request::url(['verify', 'id', 'signature', 'expires']));
    }

    /**
     * makeVerifyRateLimiter
     */
    protected function makeVerifyRateLimiter()
    {
        return new \System\Classes\RateLimiter('verify:'.$this->user()->getKey());
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
