<?php namespace Plugins\October\User\Components;

use Auth;
use Modules\Cms\Classes\ComponentBase;

class Signin extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Sign in',
            'description' => 'Allows users to sign in.'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onSignin()
    {
        $rules = [
            'email'    => 'required|email|min:2|max:64',
            'password' => 'required|min:2'
        ];

        $validation = Validator::make(Input::all(), $rules);
        if ($validation->fails())
            throw new ValidationException($validation);

        // Authenticate user
        $user = Auth::authenticate([
            'email' => post('email'),
            'password' => post('password')
        ], true);

        // Redirect to the intended page after successful sign in
        if (post('redirect'))
            return Redirect::intended(post('redirect'));
    }

}