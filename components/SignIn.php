<?php namespace RainLab\User\Components;

use Auth;
use Redirect;
use Validator;
use Cms\Classes\ComponentBase;
use October\Rain\Support\ValidationException;

class SignIn extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Sign in',
            'description' => 'User sign in form.'
        ];
    }

    public function defineProperties()
    {
        return [
            'redirect' => [
                'title' => 'Redirect to',
                'description' => 'Page name to redirect to after signing in.',
                'type' => 'string'
            ]
        ];
    }

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

}
