<?php namespace RainLab\User\Components;

use Cms;
use Auth;
use Flash;
use RainLab\User\Models\User;
use RainLab\User\Classes\ActionManager;
use Cms\Classes\ComponentBase;

/**
 * ResetPassword controls the password reset workflow
 *
 * When a user has forgotten their password, they are able to reset it using
 * a unique token that, sent to their email address upon request. This component
 * can also be used for changing the password of authenticated users.
 *
 * @package rainlab\user
 * @author Alexey Bobkov, Samuel Georges
 */
class ResetPassword extends ComponentBase
{
    /**
     * componentDetails
     */
    public function componentDetails()
    {
        return [
            'name' => "Reset Password",
            'description' => 'Confirms and resets the user with a new password.'
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
                'description' => 'Use this page as the default entry point when recovering a password.',
                'showExternalParam' => false
            ],
        ];
    }

    /**
     * onConfirmPassword
     */
    public function onConfirmPassword()
    {
        $this->actions()->resetPassword(post());

        if ($flash = Cms::flashFromPost(__("Your password has been created and you may now sign in to your account"))) {
            Flash::success($flash);
        }

        if ($redirect = Cms::redirectFromPost()) {
            return $redirect;
        }
    }

    /**
     * onResetPassword
     */
    public function onResetPassword()
    {
        $this->actions()->resetPassword(post());

        if ($flash = Cms::flashFromPost(__("Your password has been reset"))) {
            Flash::success($flash);
        }

        if ($redirect = Cms::redirectFromPost()) {
            return $redirect;
        }
    }

    /**
     * onChangePassword
     */
    public function onChangePassword()
    {
        $this->actions()->changePassword(post());

        if ($flash = Cms::flashFromPost(__("Your password has been changed"))) {
            Flash::success($flash);
        }

        if ($redirect = Cms::redirectFromPost()) {
            return $redirect;
        }
    }

    /**
     * canReset returns true if the user can reset their password
     */
    public function canReset(): bool
    {
        return $this->hasToken() || $this->user();
    }

    /**
     * hasToken checks if a reset token state is requested by the user
     */
    public function hasToken()
    {
        return $this->token() && $this->email();
    }

    /**
     * hasInvite
     */
    public function hasInvite(): bool
    {
        return (bool) get('new');
    }

    /**
     * user to check for a change password
     */
    public function user(): ?User
    {
        return Auth::user();
    }

    /**
     * email to match with a password reset token
     */
    public function email()
    {
        return get('email');
    }

    /**
     * token returns a password reset token
     */
    public function token()
    {
        return get('reset');
    }

    /**
     * actions returns user workflow services hosted by this component
     */
    protected function actions(): ActionManager
    {
        return ActionManager::instance()->withContext($this);
    }
}
