<?php namespace Plugins\RainLab\User\Components;

use Auth;
use Input;
use Validator;
use Modules\Cms\Classes\ComponentBase;
use October\Rain\Support\ValidationException;

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
        return [];
    }

    public function onRegister()
    {
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

        // Register user
        $user = Auth::register($data, true);

        // Redirect to the intended page after successful sign in
        if (post('redirect'))
            return Redirect::intended(post('redirect'));
    }

}