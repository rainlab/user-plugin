<?php namespace RainLab\User\Components;

use Auth;
use Mail;
use Input;
use Redirect;
use Validator;
use Cms\Classes\ComponentBase;
use October\Rain\Support\ValidationException;
use RainLab\User\Models\Settings as UserSettings;

class RegisterForm extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Register',
            'description' => 'User registration form.'
        ];
    }

    public function defineProperties()
    {
        return [
            'redirect-to' => [
                'title' => 'Redirect to',
                'description' => 'Page name to redirect to after registration.',
                'type' => 'string'
            ],
            'code-param' => [
                'title' => 'Activation Code Param',
                'description' => 'The page URL parameter used for the activation code',
                'type' => 'string',
                'default' => 'code'
            ]
        ];
    }

    /**
     * Executed when this component is bound to a page or layout.
     */
    public function onRun()
    {
        $routeParameter = $this->property('code-param');
        if ($activationCode = $this->param($routeParameter))
            $this->onActivate($activationCode);
    }

    public function onActivate($code = null)
    {
        if (!$code)
            $code = post('code');

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
                $this->property('code-param') => $code
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
        $redirectUrl = $this->controller->pageUrl($this->property('redirect-to'));

        if ($redirectUrl = post('redirect', $redirectUrl))
            return Redirect::intended($redirectUrl);
    }

}