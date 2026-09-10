<?php namespace RainLab\User\Components;

use Cms;
use Flash;
use Config;
use Request;
use Redirect;
use Cms\Classes\ComponentBase;
use RainLab\User\Models\Setting;
use RainLab\User\Classes\ActionManager;
use RainLab\User\Helpers\User as UserHelper;
use NotFoundException;

/**
 * Authentication displays login forms
 */
class Authentication extends ComponentBase
{
    const REMEMBER_ALWAYS = 'always';
    const REMEMBER_NEVER = 'never';
    const REMEMBER_ASK = 'ask';

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
                'type' => 'dropdown',
                'default' => static::REMEMBER_NEVER,
                'options' => [
                    self::REMEMBER_ALWAYS => "Always",
                    self::REMEMBER_NEVER => "Never",
                    self::REMEMBER_ASK => "Ask"
                ]
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
            'redirect' => [
                'title' => "Redirect To",
                'description' => "Page name to redirect to after signing in, unless overridden by the form.",
                'type' => 'dropdown',
                'default' => ''
            ],
        ];
    }

    /**
     * getRedirectOptions
     */
    public function getRedirectOptions()
    {
        return [''=>'- none -'] + \Cms\Classes\Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    /**
     * onLogin signs in the user
     */
    public function onLogin()
    {
        $options = ['remember' => $this->useRememberMe()];

        $result = $this->useTwoFactorAuth()
            ? $this->actions()->loginWithTwoFactor(post(), $options)
            : $this->actions()->login(post(), $options);

        if ($result === ActionManager::TWO_FACTOR_CHALLENGE) {
            return Redirect::to(Request::fullUrlWithQuery([
                'two-factor' => 'challenge'
            ]));
        }

        if ($result) {
            return $result;
        }

        if ($redirect = Cms::redirectIntendedFromPost($this->makeRedirectUrl())) {
            return $redirect;
        }
    }

    /**
     * makeRedirectUrl resolves the redirect property to a URL, or null when unset
     */
    protected function makeRedirectUrl(): ?string
    {
        if (!$page = $this->property('redirect')) {
            return null;
        }

        return Cms::pageUrl($page);
    }

    /**
     * onTwoFactorChallenge
     */
    public function onTwoFactorChallenge()
    {
        if (!$this->useTwoFactorAuth()) {
            throw new NotFoundException;
        }

        if ($response = $this->actions()->twoFactorChallenge(post(), ['remember' => $this->useRememberMe()])) {
            return $response;
        }

        if ($redirect = Cms::redirectIntendedFromPost($this->makeRedirectUrl())) {
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

        $this->actions()->recoverPassword(post());

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
        return $this->useTwoFactorAuth() && get('two-factor') === 'challenge' && $this->actions()->hasChallengedUser();
    }

    /**
     * useTwoFactorAuth
     */
    public function useTwoFactorAuth(): bool
    {
        if (($config = Config::get('rainlab.user::force_two_factor_auth')) !== null) {
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
     * useRememberMe returns true if the user session should be persisted with a cookie
     */
    public function useRememberMe(): bool
    {
        if ($this->showRememberMe()) {
            return (bool) input('remember');
        }

        return $this->property('rememberMe') !== self::REMEMBER_NEVER;
    }

    /**
     * showRememberMe gives the user the option to trust the device or not
     */
    public function showRememberMe(): bool
    {
        return $this->property('rememberMe') === self::REMEMBER_ASK;
    }

    /**
     * showUsernameField
     */
    public function showUsernameField()
    {
        return UserHelper::showUsername();
    }

    /**
     * canRegister checks if the registration is allowed
     */
    public function canRegister(): bool
    {
        return Setting::get('allow_registration');
    }

    /**
     * actions returns user workflow services hosted by this component
     */
    protected function actions(): ActionManager
    {
        return ActionManager::instance()->withContext($this);
    }

    /**
     * @deprecated use onLogin
     */
    public function onSignin()
    {
        return $this->onLogin();
    }
}
