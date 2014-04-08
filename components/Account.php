<?php namespace RainLab\User\Components;

use Auth;
use Redirect;
use Cms\Classes\ComponentBase;

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
                'description' => 'Page name to redirect to after sign in or registration.',
                'type'        => 'string'
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
     * @var RainLab\User\Models\User The user model
     */
    public $user;

    /**
     * Executed when this component is bound to a page or layout.
     */
    public function onRun()
    {
        $this->user = Auth::getUser();

        $routeParameter = $this->property('paramCode');
        if ($activationCode = $this->param($routeParameter))
            $this->onActivate($activationCode);
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

    /**
     * Update the user
     */
    public function onUpdate()
    {
        if ($user = $this->user)
            $user->save(post());

        if ($redirectUrl = post('redirect')) {
            Flash::success(post('flash', 'Settings successfully saved!'));

            return Redirect::to($redirectUrl);
        }
    }

}