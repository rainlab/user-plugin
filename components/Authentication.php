<?php namespace RainLab\User\Components;

use Cms;
use Flash;
use Config;
use Cms\Classes\ComponentBase;
use NotFoundException;

/**
 * Authentication displays login forms
 */
class Authentication extends ComponentBase
{
    use \RainLab\User\Components\Authentication\ActionLogin;
    use \RainLab\User\Components\Authentication\ActionTwoFactorLogin;
    use \RainLab\User\Components\Authentication\ActionRecoverPassword;

    /**
     * componentDetails
     */
    public function componentDetails()
    {
        return [
            'name' => "Authentication",
            'description' => "Provides services for logging a user in."
        ];
    }

    /**
     * defineProperties
     */
    public function defineProperties()
    {
        return [
            'rememberMe' => [
                'title' => "Remember Me",
                'description' => "Ask the user if they want to stay logged in after the browser is closed.",
                'type' => 'checkbox',
                'default' => false
            ],
            'twoFactorAuth' => [
                'title' => "Two-Factor Authentication",
                'description' => "Check this box to enable two-factor authentication when logging in, if the user has it set up.",
                'type' => 'checkbox',
                'default' => true
            ],
            'recoverPassword' => [
                'title' => "Password Recovery",
                'description' => "Check this box to allow users to reset their own passwords.",
                'type' => 'checkbox',
                'default' => true
            ],
        ];
    }

    /**
     * onRun
     */
    public function onRun()
    {
        if ($redirect = $this->checkRecoveryPasswordRedirect()) {
            return $redirect;
        }
    }

    /**
     * onLogin signs in the user
     */
    public function onLogin()
    {
        if ($this->useTwoFactorAuth()) {
            if ($response = $this->actionLoginWithTwoFactor()) {
                return $response;
            }
        }
        elseif ($response = $this->actionLogin()) {
            return $response;
        }

        if ($redirect = Cms::redirectIntendedFromPost()) {
            return $redirect;
        }
    }

    /**
     * onTwoFactorChallenge
     */
    public function onTwoFactorChallenge()
    {
        if (!$this->useTwoFactorAuth()) {
            throw new NotFoundException;
        }

        if ($response = $this->actionTwoFactorChallenge()) {
            return $response;
        }

        if ($redirect = Cms::redirectIntendedFromPost()) {
            return $redirect;
        }
    }

    /**
     * onRecoverPassword starts the process to reset the user password
     */
    public function onRecoverPassword()
    {
        if (!$this->usePasswordRecovery()) {
            throw new NotFoundException;
        }

        if ($response = $this->actionRecoverPassword()) {
            return $response;
        }

        if ($flash = Cms::flashFromPost(__("Please check your email. We have sent instructions to reset your password."))) {
            Flash::success($flash);
        }

        if ($redirect = Cms::redirectFromPost()) {
            return $redirect;
        }
    }

    /**
     * showLoginForm
     */
    public function showLoginForm(): bool
    {
        return !$this->showTwoFactorChallenge();
    }

    /**
     * showTwoFactorChallenge
     */
    public function showTwoFactorChallenge(): bool
    {
        return $this->useTwoFactorAuth() && get('two-factor') === 'challenge' && $this->hasChallengedUser();
    }

    /**
     * useTwoFactorAuth
     */
    public function useTwoFactorAuth(): bool
    {
        if (($config = Config::get('user.force_two_factor_auth')) !== null) {
            return (bool) $config;
        }

        return (bool) $this->property('twoFactorAuth', true);
    }

    /**
     * usePasswordRecovery returns true if the user can reset their password using self-service
     */
    public function usePasswordRecovery(): bool
    {
        return (bool) $this->property('recoverPassword', true);
    }

    /**
     * useRememberMe returns true if the user can opt to trust the device or not
     */
    public function useRememberMe(): bool
    {
        return (bool) $this->property('rememberMe', false);
    }
}
