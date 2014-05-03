<?php namespace RainLab\User\Components;

use Auth;
use Mail;
use Flash;
use Redirect;
use Validator;
use Cms\Classes\ComponentBase;
use System\Classes\ApplicationException;
use October\Rain\Support\ValidationException;
use RainLab\User\Models\Settings as UserSettings;

class Account extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name'        => 'Account',
            'description' => 'User management form.'
        ];
    }

    public function defineProperties()
    {
        return [
            'redirect' => [
                'title'       => 'Redirect to',
                'description' => 'Page name to redirect to after update, sign in or registration.',
                'type'        => 'string' // @todo Page picker
            ],
            'paramCode' => [
                'title'       => 'Activation Code Param',
                'description' => 'The page URL parameter used for the registration activation code',
                'type'        => 'string',
                'default'     => 'code'
            ]
        ];
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
     * Sign in the user
     */
    public function onSignin()
    {
        /*
         * Validate input
         */
        $rules = [
            'email'    => 'required|email|min:2|max:64',
            'password' => 'required|min:2'
        ];

        $validation = Validator::make(post(), $rules);
        if ($validation->fails())
            throw new ValidationException($validation);

        /*
         * Authenticate user
         */
        $user = Auth::authenticate([
            'email' => post('email'),
            'password' => post('password')
        ], true);

        /*
         * Redirect to the intended page after successful sign in
         */
        $redirectUrl = $this->controller->pageUrl($this->property('redirect'));

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

        if (!post('password_confirmation'))
            $data['password_confirmation'] = post('password');

        $rules = [
            'email'    => 'required|email|min:2|max:64',
            'password' => 'required|min:2'
        ];

        $validation = Validator::make($data, $rules);
        if ($validation->fails())
            throw new ValidationException($validation);

        /*
         * Register user
         */
        $automaticActivation = UserSettings::get('auto_activation', true);
        $user = Auth::register($data, $automaticActivation);

        /*
         * Activation is required, send the email
         */
        if (!$automaticActivation) {

            $code = implode('!', [$user->id, $user->getActivationCode()]);
            $link = $this->controller->currentPageUrl([
                $this->property('paramCode') => $code
            ]);

            $data = [
                'name' => $user->name,
                'link' => $link,
                'code' => $code
            ];

            Mail::send(['text' => 'rainlab.user::emails.activate'], $data, function($message) use ($user)
            {
                $message->to($user->email, $user->name)->subject('Confirm Your Account');
            });
        }
        /*
         * Automatically activated, log the user in
         */
        else {
            Auth::login($user);
        }

        /*
         * Redirect to the intended page after successful sign in
         */
        $redirectUrl = $this->controller->pageUrl($this->property('redirect'));

        if ($redirectUrl = post('redirect', $redirectUrl))
            return Redirect::intended($redirectUrl);
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
            if (count($parts) != 2)
                throw new ValidationException(['code' => 'Invalid activation code supplied']);

            list($userId, $code) = $parts;

            if (!strlen(trim($userId)) || !($user = Auth::findUserById($userId)))
                throw new ApplicationException('A user was not found with the given credentials.');

            if (!$user->attemptActivation($code))
                throw new ValidationException(['code' => 'Invalid activation code supplied']);

            /*
             * Sign in the user
             */
            Auth::login($user);

        }
        catch (\Exception $ex) {
            if ($isAjax) throw $ex;
            else Flash::error($ex->getMessage());
        }
    }

    /**
     * Update the user
     */
    public function onUpdate()
    {
        if ($user = $this->user())
            $user->save(post());

        Flash::success(post('flash', 'Settings successfully saved!'));

        /*
         * Redirect to the intended page after successful update
         */
        $redirectUrl = $this->controller->pageUrl($this->property('redirect'));

        if ($redirectUrl = post('redirect', $redirectUrl))
            return Redirect::to($redirectUrl);
    }

}