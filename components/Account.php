<?php namespace RainLab\User\Components;

use Cms;
use Auth;
use Flash;
use Request;
use Redirect;
use RainLab\User\Models\User;
use RainLab\User\Classes\ActionManager;
use Cms\Classes\ComponentBase;
use ApplicationException;
use ValidationException;

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
        $response = $this->actions()->updateProfile((array) post(), [
            'avatar' => files('avatar'),
            'removeAvatar' => post('remove_avatar'),
        ]);

        if ($response) {
            return $response;
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
        $this->actions()->sendVerifyEmail();

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
            $this->actions()->confirmVerifiedEmail(post('verify'));
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

        $this->actions()->enableTwoFactor();

        $this->page['showConfirmation'] = true;
    }

    /**
     * onConfirmTwoFactor
     */
    public function onConfirmTwoFactor()
    {
        $this->actions()->confirmTwoFactor(post());

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
        $this->actions()->regenerateTwoFactorRecoveryCodes();

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

        $this->actions()->disableTwoFactor();
    }

    /**
     * onDeleteOtherSessions from storage.
     */
    protected function onDeleteOtherSessions()
    {
        $this->actions()->deleteOtherSessions(post());

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
        $this->actions()->deleteUser(post());

        if ($flash = Cms::flashFromPost(__("Your account has been removed from our system."))) {
            Flash::success($flash);
        }

        if ($redirect = Cms::redirectFromPost()) {
            return $redirect;
        }
    }

    /**
     * checkVerifyEmailRedirect verifies the email address using a code found in
     * the page URL, then redirects to remove the code
     */
    protected function checkVerifyEmailRedirect()
    {
        $verifyCode = get('verify');
        if (!$verifyCode) {
            return;
        }

        try {
            $this->actions()->confirmVerifiedEmail($verifyCode);

            if ($flash = Cms::flashFromPost(__("Thank you for verifying your email."))) {
                Flash::success($flash);
            }
        }
        catch (ApplicationException $ex) {
            Flash::error($ex->getMessage());
        }

        if (in_array(get('redirect'), ['0', 'false'])) {
            return;
        }

        $redirectUrl = rtrim(Request::fullUrlWithQuery(['verify' => null]), '?');
        return Redirect::to($redirectUrl);
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
        return $this->actions()->getBrowserSessions();
    }

    /**
     * twoFactorEnabled returns true if the user has two factor enabled
     */
    public function twoFactorEnabled(): bool
    {
        return $this->actions()->hasTwoFactorEnabled();
    }

    /**
     * twoFactorRecoveryCodes returns an array of recovery codes, if available
     */
    public function twoFactorRecoveryCodes(): array
    {
        return $this->actions()->getTwoFactorRecoveryCodes();
    }

    /**
     * actions returns user workflow services hosted by this component
     */
    protected function actions(): ActionManager
    {
        return ActionManager::instance()->withContext($this);
    }
}
