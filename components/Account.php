<?php namespace RainLab\User\Components;

use Lang;
use Auth;
use Mail;
use Event;
use Flash;
use Input;
use Request;
use Redirect;
use Validator;
use ValidationException;
use ApplicationException;
use October\Rain\Auth\AuthException;
use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use RainLab\User\Models\User as UserModel;
use RainLab\User\Models\Settings as UserSettings;
use Exception;

/**
 * Account component
 *
 * Allows users to register, sign in and update their account. They can also
 * deactivate their account and resend the account verification email.
 */
class Account extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => /*Account*/'rainlab.user::lang.account.account',
            'description' => /*User management form.*/'rainlab.user::lang.account.account_desc'
        ];
    }

    public function defineProperties()
    {
        return [
            'redirect' => [
                'title'       => /*Redirect to*/'rainlab.user::lang.account.redirect_to',
                'description' => /*Page name to redirect to after update, sign in or registration.*/'rainlab.user::lang.account.redirect_to_desc',
                'type'        => 'dropdown',
                'default'     => ''
            ],
            'paramCode' => [
                'title'       => /*Activation Code Param*/'rainlab.user::lang.account.code_param',
                'description' => /*The page URL parameter used for the registration activation code*/ 'rainlab.user::lang.account.code_param_desc',
                'type'        => 'string',
                'default'     => 'code'
            ],
            'activationPage' => [
                'title'       => /* Activation Page */'rainlab.user::lang.account.activation_page',
                'description' => /* Select a page to use for activating the user account */'rainlab.user::lang.account.activation_page_comment',
                'type'        => 'dropdown',
                'default'     => ''
            ],
            'forceSecure' => [
                'title'       => /*Force secure protocol*/'rainlab.user::lang.account.force_secure',
                'description' => /*Always redirect the URL with the HTTPS schema.*/'rainlab.user::lang.account.force_secure_desc',
                'type'        => 'checkbox',
                'default'     => 0
            ],
            'requirePassword' => [
                'title'       => /*Confirm password on update*/'rainlab.user::lang.account.update_requires_password',
                'description' => /*Require the current password of the user when changing their profile.*/'rainlab.user::lang.account.update_requires_password_comment',
                'type'        => 'checkbox',
                'default'     => 0
            ],
        ];
    }

    /**
     * getRedirectOptions
     */
    public function getRedirectOptions()
    {
        return [
            '' => '- refresh page -',
            '0' => '- no redirect -'
        ] + Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    /**
     * getActivationPageOptions
     */
    public function getActivationPageOptions()
    {
        return [
            '' => '- current page -',
        ] + Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    /**
     * Executed when this component is initialized
     */
    public function prepareVars()
    {
        $this->page['user'] = $this->user();
        $this->page['canRegister'] = $this->canRegister();
        $this->page['loginAttribute'] = $this->loginAttribute();
        $this->page['loginAttributeLabel'] = $this->loginAttributeLabel();
        $this->page['updateRequiresPassword'] = $this->updateRequiresPassword();
        $this->page['rememberLoginMode'] = $this->rememberLoginMode();
    }

    /**
     * Executed when this component is bound to a page or layout.
     */
    public function onRun()
    {
        /*
         * Redirect to HTTPS checker
         */
        if ($redirect = $this->redirectForceSecure()) {
            return $redirect;
        }

        /*
         * Activation code supplied
         */
        if ($code = $this->activationCode()) {
            $this->onActivate($code);
        }

        $this->prepareVars();
    }

    //
    // Properties
    //

    /**
     * Returns the logged in user, if available
     */
    public function user()
    {
        if (!Auth::check()) {
            return null;
        }

        return Auth::getUser();
    }

    /**
     * Flag for allowing registration, pulled from UserSettings
     */
    public function canRegister()
    {
        return UserSettings::get('allow_registration', true);
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
        return Lang::get($this->loginAttribute() == UserSettings::LOGIN_EMAIL
            ? /*Email*/'rainlab.user::lang.login.attribute_email'
            : /*Username*/'rainlab.user::lang.login.attribute_username'
        );
    }

    /**
     * Returns the update requires password setting
     */
    public function updateRequiresPassword()
    {
        return $this->property('requirePassword', false);
    }

    /**
     * Returns the login remember mode.
     */
    public function rememberLoginMode()
    {
        return UserSettings::get('remember_login', UserSettings::REMEMBER_ALWAYS);
    }

    /**
     * useRememberLogin returns true if persistent authentication should be used.
     */
    protected function useRememberLogin(): bool
    {
        switch ($this->rememberLoginMode()) {
            case UserSettings::REMEMBER_ALWAYS:
                return true;

            case UserSettings::REMEMBER_NEVER:
                return false;

            case UserSettings::REMEMBER_ASK:
                return (bool) post('remember', false);
        }
    }

    /**
     * Looks for the activation code from the URL parameter. If nothing
     * is found, the GET parameter 'activate' is used instead.
     * @return string
     */
    public function activationCode()
    {
        $routeParameter = $this->property('paramCode');

        if ($code = $this->param($routeParameter)) {
            return $code;
        }

        return get('activate');
    }

    //
    // AJAX
    //

    /**
     * Sign in the user
     */
    public function onSignin()
    {
        try {
            /*
             * Validate input
             */
            $data = post();
            $rules = [];

            $rules['login'] = $this->loginAttribute() == UserSettings::LOGIN_USERNAME
                ? 'required|between:2,255'
                : 'required|email|between:6,255';

            $rules['password'] = 'required|between:' . UserModel::getMinPasswordLength() . ',255';

            if (!array_key_exists('login', $data)) {
                $data['login'] = post('username', post('email'));
            }

            $data['login'] = trim($data['login']);

            $validation = Validator::make(
                $data,
                $rules,
                $this->getValidatorMessages(),
                $this->getCustomAttributes()
            );

            if ($validation->fails()) {
                throw new ValidationException($validation);
            }

            /*
             * Authenticate user
             */
            $credentials = [
                'login'    => array_get($data, 'login'),
                'password' => array_get($data, 'password')
            ];

            Event::fire('rainlab.user.beforeAuthenticate', [$this, $credentials]);

            $user = Auth::authenticate($credentials, $this->useRememberLogin());
            if ($user->isBanned()) {
                Auth::logout();
                throw new AuthException(Lang::get(/*Sorry, this user is currently not activated. Please contact us for further assistance.*/'rainlab.user::lang.account.banned'));
            }

            /*
             * Record IP address
             */
            if ($ipAddress = Request::ip()) {
                $user->touchIpAddress($ipAddress);
            }

            /*
             * Redirect
             */
            if ($redirect = $this->makeRedirection(true)) {
                return $redirect;
            }
        }
        catch (Exception $ex) {
            if (Request::ajax()) throw $ex;
            else Flash::error($ex->getMessage());
        }
    }

    /**
     * Register the user
     */
    public function onRegister()
    {
        try {
            if (!$this->canRegister()) {
                throw new ApplicationException(Lang::get(/*Registrations are currently disabled.*/'rainlab.user::lang.account.registration_disabled'));
            }

            if ($this->isRegisterThrottled()) {
                throw new ApplicationException(Lang::get(/*Registration is throttled. Please try again later.*/'rainlab.user::lang.account.registration_throttled'));
            }

            /*
             * Validate input
             */
            $data = post();

            if (!array_key_exists('password_confirmation', $data)) {
                $data['password_confirmation'] = post('password');
            }

            $rules = (new UserModel)->rules;

            if ($this->loginAttribute() !== UserSettings::LOGIN_USERNAME) {
                unset($rules['username']);
            }

            $validation = Validator::make(
                $data,
                $rules,
                $this->getValidatorMessages(),
                $this->getCustomAttributes()
            );

            if ($validation->fails()) {
                throw new ValidationException($validation);
            }

            /*
             * Record IP address
             */
            if ($ipAddress = Request::ip()) {
                $data['created_ip_address'] = $data['last_ip_address'] = $ipAddress;
            }

            /*
             * Register user
             */
            Event::fire('rainlab.user.beforeRegister', [&$data]);

            $requireActivation = UserSettings::get('require_activation', true);
            $automaticActivation = UserSettings::get('activate_mode') == UserSettings::ACTIVATE_AUTO;
            $userActivation = UserSettings::get('activate_mode') == UserSettings::ACTIVATE_USER;
            $adminActivation = UserSettings::get('activate_mode') == UserSettings::ACTIVATE_ADMIN;
            $user = Auth::register($data, $automaticActivation);

            Event::fire('rainlab.user.register', [$user, $data]);

            /*
             * Activation is by the user, send the email
             */
            if ($userActivation) {
                $this->sendActivationEmail($user);

                Flash::success(Lang::get(/*An activation email has been sent to your email address.*/'rainlab.user::lang.account.activation_email_sent'));
            }

            $intended = false;

            /*
             * Activation is by the admin, show message
             * For automatic email on account activation RainLab.Notify plugin is needed
             */
            if ($adminActivation) {
                Flash::success(Lang::get(/*You have successfully registered. Your account is not yet active and must be approved by an administrator.*/'rainlab.user::lang.account.activation_by_admin'));
            }

            /*
             * Automatically activated or not required, log the user in
             */
            if ($automaticActivation || !$requireActivation) {
                Auth::login($user, $this->useRememberLogin());
                $intended = true;
            }

            /*
             * Redirect to the intended page after successful sign in
             */
            if ($redirect = $this->makeRedirection($intended)) {
                return $redirect;
            }
        }
        catch (Exception $ex) {
            if (Request::ajax()) throw $ex;
            else Flash::error($ex->getMessage());
        }
    }

    /**
     * Activate the user
     * @param  string $code Activation code
     */
    public function onActivate($code = null)
    {
        try {
            $code = post('code', $code);

            $errorFields = ['code' => Lang::get(/*Invalid activation code supplied.*/'rainlab.user::lang.account.invalid_activation_code')];

            /*
             * Break up the code parts
             */
            $parts = explode('!', $code);
            if (count($parts) != 2) {
                throw new ValidationException($errorFields);
            }

            list($userId, $code) = $parts;

            if (!strlen(trim($userId)) || !strlen(trim($code))) {
                throw new ValidationException($errorFields);
            }

            if (!$user = Auth::findUserById($userId)) {
                throw new ValidationException($errorFields);
            }

            if (!$user->attemptActivation($code)) {
                throw new ValidationException($errorFields);
            }

            Flash::success(Lang::get(/*Successfully activated your account.*/'rainlab.user::lang.account.success_activation'));

            /*
             * Sign in the user
             */
            Auth::login($user, $this->useRememberLogin());

        }
        catch (Exception $ex) {
            if (Request::ajax()) throw $ex;
            else Flash::error($ex->getMessage());
        }
    }

    /**
     * Update the user
     */
    public function onUpdate()
    {
        if (!$user = $this->user()) {
            return;
        }

        $data = post();

        if ($this->updateRequiresPassword()) {
            if (!$user->checkHashValue('password', $data['password_current'])) {
                throw new ValidationException(['password_current' => Lang::get('rainlab.user::lang.account.invalid_current_pass')]);
            }
        }

        if (Input::hasFile('avatar')) {
            $user->avatar = Input::file('avatar');
        }

        $user->fill($data);
        $user->save();

        /*
         * Password has changed, reauthenticate the user
         */
        if (array_key_exists('password', $data) && strlen($data['password'])) {
            Auth::login($user->reload(), $this->useRememberLogin());
        }

        /*
         * Update Event to hook into the plugins function
         */
        Event::fire('rainlab.user.update', [$user, $data]);

        Flash::success(post('flash', Lang::get(/*Settings successfully saved!*/'rainlab.user::lang.account.success_saved')));

        /*
         * Redirect
         */
        if ($redirect = $this->makeRedirection()) {
            return $redirect;
        }

        $this->prepareVars();
    }

    /**
     * Deactivate user
     */
    public function onDeactivate()
    {
        if (!$user = $this->user()) {
            return;
        }

        if (!$user->checkHashValue('password', post('password'))) {
            throw new ValidationException(['password' => Lang::get('rainlab.user::lang.account.invalid_deactivation_pass')]);
        }

        Auth::logout();
        $user->delete();

        Flash::success(post('flash', Lang::get(/*Successfully deactivated your account. Sorry to see you go!*/'rainlab.user::lang.account.success_deactivation')));

        /*
         * Redirect
         */
        if ($redirect = $this->makeRedirection()) {
            return $redirect;
        }
    }

    /**
     * Trigger a subsequent activation email
     */
    public function onSendActivationEmail()
    {
        try {
            if (!$user = $this->user()) {
                throw new ApplicationException(Lang::get(/*You must be logged in first!*/'rainlab.user::lang.account.login_first'));
            }

            if ($user->is_activated) {
                throw new ApplicationException(Lang::get(/*Your account is already activated!*/'rainlab.user::lang.account.already_active'));
            }

            Flash::success(Lang::get(/*An activation email has been sent to your email address.*/'rainlab.user::lang.account.activation_email_sent'));

            $this->sendActivationEmail($user);

        }
        catch (Exception $ex) {
            if (Request::ajax()) throw $ex;
            else Flash::error($ex->getMessage());
        }

        /*
         * Redirect
         */
        if ($redirect = $this->makeRedirection()) {
            return $redirect;
        }
    }

    //
    // Helpers
    //

    /**
     * Returns a link used to activate the user account.
     * @return string
     */
    protected function makeActivationUrl($code)
    {
        $params = [
            $this->property('paramCode') => $code
        ];

        if ($pageName = $this->property('activationPage')) {
            $url = $this->pageUrl($pageName, $params);
        }
        else {
            $url = $this->currentPageUrl($params);
        }

        if (strpos($url, $code) === false) {
            $url .= '?activate=' . $code;
        }

        return $url;
    }

    /**
     * Sends the activation email to a user
     * @param  User $user
     * @return void
     */
    protected function sendActivationEmail($user)
    {
        $code = implode('!', [$user->id, $user->getActivationCode()]);

        $link = $this->makeActivationUrl($code);

        $data = [
            'name' => $user->name,
            'link' => $link,
            'code' => $code
        ];

        Mail::send('rainlab.user::mail.activate', $data, function($message) use ($user) {
            $message->to($user->email, $user->name);
        });
    }

    /**
     * Redirect to the intended page after successful update, sign in or registration.
     * The URL can come from the "redirect" property or the "redirect" postback value.
     * @return mixed
     */
    protected function makeRedirection($intended = false)
    {
        $method = $intended ? 'intended' : 'to';

        $property = post('redirect', $this->property('redirect'));

        // No redirect
        if ($property === '0') {
            return;
        }

        // Refresh page
        if ($property === '') {
            return Redirect::refresh();
        }

        $redirectUrl = $this->pageUrl($property) ?: $property;

        if ($redirectUrl) {
            return Redirect::$method($redirectUrl);
        }
    }

    /**
     * Checks if the force secure property is enabled and if so
     * returns a redirect object.
     * @return mixed
     */
    protected function redirectForceSecure()
    {
        if (
            Request::secure() ||
            Request::ajax() ||
            !$this->property('forceSecure')
        ) {
            return;
        }

        return Redirect::secure(Request::path());
    }

    /**
     * Returns true if user is throttled.
     * @return bool
     */
    protected function isRegisterThrottled()
    {
        if (!UserSettings::get('use_register_throttle', false)) {
            return false;
        }

        return UserModel::isRegisterThrottled(Request::ip());
    }

    /**
     * getValidatorMessages
     */
    protected function getValidatorMessages(): array
    {
        return (array) (new UserModel)->customMessages;
    }

    /**
     * getCustomAttributes
     */
    protected function getCustomAttributes(): array
    {
        return [
            'login' => $this->loginAttributeLabel(),
            'password' => Lang::get('rainlab.user::lang.account.password'),
            'email' => Lang::get('rainlab.user::lang.account.email'),
            'username' => Lang::get('rainlab.user::lang.user.username'),
            'name' => Lang::get('rainlab.user::lang.account.full_name')
        ];
    }
}
