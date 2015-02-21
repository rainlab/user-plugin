<?php namespace RainLab\User\Components;

use Lang;
use Auth;
use Mail;
use Flash;
use Input;
use Redirect;
use Validator;
use ValidationException;
use ApplicationException;
use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use RainLab\User\Models\Settings as UserSettings;
use Exception;

class Account extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name'        => 'rainlab.user::lang.account.account',
            'description' => 'rainlab.user::lang.account.account_desc'
        ];
    }

    public function defineProperties()
    {
        return [
            'redirect' => [
                'title'       => 'rainlab.user::lang.account.redirect_to',
                'description' => 'rainlab.user::lang.account.redirect_to_desc',
                'type'        => 'dropdown',
                'default'     => ''
            ],
            'paramCode' => [
                'title'       => 'rainlab.user::lang.account.code_param',
                'description' => 'rainlab.user::lang.account.code_param_desc',
                'type'        => 'string',
                'default'     => 'code'
            ]
        ];
    }

    public function getRedirectOptions()
    {
        return [''=>'- none -'] + Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    /**
     * Executed when this component is bound to a page or layout.
     */
    public function onRun()
    {
        $routeParameter = $this->property('paramCode');

        /*
         * Activation code supplied
         */
        if ($activationCode = $this->param($routeParameter)) {
            $this->onActivate(false, $activationCode);
        }

        $this->page['user'] = $this->user();
        $this->page['loginAttribute'] = $this->loginAttribute();
        $this->page['loginAttributeLabel'] = $this->loginAttributeLabel();
    }

    /**
     * Returns the logged in user, if available
     */
    public function user()
    {
        if (!Auth::check())
            return null;

        return Auth::getUser();
    }

    /**
     * Returns the login model attribute.
     */
    public function loginAttribute()
    {
        return UserSettings::get('login_attribute', UserSettings::LOGIN_EMAIL);
    }

    /**
     * Returns the login label as a word.
     */
    public function loginAttributeLabel()
    {
        return $this->loginAttribute() == UserSettings::LOGIN_EMAIL
            ? Lang::get('rainlab.user::lang.login.attribute_email')
            : Lang::get('rainlab.user::lang.login.attribute_username');
    }

    /**
     * Sign in the user
     */
    public function onSignin()
    {
        /*
         * Validate input
         */
        $data = post();
        $rules = [];

        $rules['login'] = $this->loginAttribute() == UserSettings::LOGIN_USERNAME
            ? 'required|between:2,64'
            : 'required|email|between:2,64';

        $rules['password'] = 'required|min:2';

        if (!array_key_exists('login', $data)) {
            $data['login'] = post('username', post('email'));
        }

        $validation = Validator::make($data, $rules);
        if ($validation->fails()) {
            throw new ValidationException($validation);
        }

        /*
         * Authenticate user
         */
        $user = Auth::authenticate([
            'login' => array_get($data, 'login'),
            'password' => array_get($data, 'password')
        ], true);

        /*
         * Redirect to the intended page after successful sign in
         */
        $redirectUrl = $this->pageUrl($this->property('redirect'));

        if ($redirectUrl = post('redirect', $redirectUrl))
            return Redirect::intended($redirectUrl);
    }

    /**
     * Register the user
     */
    public function onRegister()
    {
        /*
         * Validate input
         */
        $data = post();

        if (!array_key_exists('password_confirmation', $data)) {
            $data['password_confirmation'] = post('password');
        }

        $rules = [
            'email'    => 'required|email|between:2,64',
            'password' => 'required|min:2'
        ];

        if ($this->loginAttribute() == UserSettings::LOGIN_USERNAME) {
            $rules['username'] = 'required|between:2,64';
        }

        $validation = Validator::make($data, $rules);
        if ($validation->fails()) {
            throw new ValidationException($validation);
        }

        /*
         * Register user
         */
        $requireActivation = UserSettings::get('require_activation', true);
        $automaticActivation = UserSettings::get('activate_mode') == UserSettings::ACTIVATE_AUTO;
        $userActivation = UserSettings::get('activate_mode') == UserSettings::ACTIVATE_USER;
        $user = Auth::register($data, $automaticActivation);

        /*
         * Activation is by the user, send the email
         */
        if ($userActivation) {
            $this->sendActivationEmail($user);

            Flash::success(Lang::get('rainlab.user::lang.account.activation_email_sent'));
        }

        /*
         * Automatically activated or not required, log the user in
         */
        if ($automaticActivation || !$requireActivation) {
            Auth::login($user);
        }

        /*
         * Redirect to the intended page after successful sign in
         */
        $redirectUrl = $this->pageUrl($this->property('redirect'));

        if ($redirectUrl = post('redirect', $redirectUrl)) {
            return Redirect::intended($redirectUrl);
        }
    }

    /**
     * Activate the user
     * @param  string $code Activation code
     */
    public function onActivate($isAjax = true, $code = null)
    {
        try {
            $code = post('code', $code);

            /*
             * Break up the code parts
             */
            $parts = explode('!', $code);
            if (count($parts) != 2) {
                throw new ValidationException(['code' => Lang::get('rainlab.user::lang.account.invalid_activation_code')]);
            }

            list($userId, $code) = $parts;

            if (!strlen(trim($userId)) || !($user = Auth::findUserById($userId))) {
                throw new ApplicationException(Lang::get('rainlab.user::lang.account.invalid_user'));
            }

            if (!$user->attemptActivation($code)) {
                throw new ValidationException(['code' => Lang::get('rainlab.user::lang.account.invalid_activation_code')]);
            }

            Flash::success(Lang::get('rainlab.user::lang.account.success_activation'));

            /*
             * Sign in the user
             */
            Auth::login($user);

        }
        catch (Exception $ex) {
            if ($isAjax) throw $ex;
            else Flash::error($ex->getMessage());
        }
    }

    /**
     * Update the user
     */
    public function onUpdate()
    {
        if (!$user = $this->user())
            return;

        $user->save(post());

        /*
         * Password has changed, reauthenticate the user
         */
        if (strlen(post('password'))) {
            Auth::login($user->reload(), true);
        }

        Flash::success(post('flash', Lang::get('rainlab.user::lang.account.success_saved')));

        /*
         * Redirect to the intended page after successful update
         */
        $redirectUrl = $this->pageUrl($this->property('redirect'));

        if ($redirectUrl = post('redirect', $redirectUrl))
            return Redirect::to($redirectUrl);
    }

    /**
     * Trigger a subsequent activation email
     */
    public function onSendActivationEmail($isAjax = true)
    {
        try {
            if (!$user = $this->user()) {
                throw new ApplicationException(Lang::get('rainlab.user::lang.account.login_first'));
            }

            if ($user->is_activated) {
                throw new ApplicationException(Lang::get('rainlab.user::lang.account.alredy_active'));
            }

            Flash::success(Lang::get('rainlab.user::lang.account.activation_email_sent'));

            $this->sendActivationEmail($user);

        }
        catch (Exception $ex) {
            if ($isAjax) throw $ex;
            else Flash::error($ex->getMessage());
        }

        /*
         * Redirect
         */
        $redirectUrl = $this->pageUrl($this->property('redirect'));

        if ($redirectUrl = post('redirect', $redirectUrl))
            return Redirect::to($redirectUrl);
    }

    /**
     * Sends the activation email to a user
     * @param  User $user
     * @return void
     */
    protected function sendActivationEmail($user)
    {
        $code = implode('!', [$user->id, $user->getActivationCode()]);
        $link = $this->currentPageUrl([
            $this->property('paramCode') => $code
        ]);

        $data = [
            'name' => $user->name,
            'link' => $link,
            'code' => $code
        ];

        Mail::send('rainlab.user::mail.activate', $data, function($message) use ($user)
        {
            $message->to($user->email, $user->name);
        });
    }

}
