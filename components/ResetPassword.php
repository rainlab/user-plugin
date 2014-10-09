<?php namespace RainLab\User\Components;

use Auth;
use Mail;
use Validator;
use Cms\Classes\ComponentBase;
use October\Rain\Support\ValidationException;
use System\Classes\ApplicationException;

class ResetPassword extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name'        => e(trans('rainlab.user::lang.reset_password.reset_password')),
            'description' => e(trans('rainlab.user::lang.reset_password.reset_password_desc'))
        ];
    }

    public function defineProperties()
    {
        return [
            'paramCode' => [
                'title'       => e(trans('rainlab.user::lang.reset_password.code_param')),
                'description' => e(trans('rainlab.user::lang.reset_password.code_param_desc')),
                'type'        => 'string',
                'default'     => 'code'
            ]
        ];
    }

    /**
     * Trigger the password reset email
     */
    public function onRestorePassword()
    {
        $rules = [
            'email' => 'required|email|min:2|max:32'
        ];

        $validation = Validator::make(post(), $rules);
        if ($validation->fails())
            throw new ValidationException($validation);

        if (!($user = Auth::findUserByLogin(post('email'))))
            throw new ApplicationException(e(trans('rainlab.user::lang.account.invalid_user')));

        $code = implode('!', [$user->id, $user->getResetPasswordCode()]);
        $link = $this->controller->currentPageUrl([
            $this->property('paramCode') => $code
        ]);

        $data = [
            'name' => $user->name,
            'link' => $link,
            'code' => $code
        ];

        Mail::send('rainlab.user::mail.restore', $data, function($message) use ($user)
        {
            $message->to($user->email, $user->full_name);
        });
    }

    /**
     * Perform the password reset
     */
    public function onResetPassword()
    {
        $rules = [
            'code' => 'required',
            'password' => 'required|min:2'
        ];

        $validation = Validator::make(post(), $rules);
        if ($validation->fails())
            throw new ValidationException($validation);

        /*
         * Break up the code parts
         */
        $parts = explode('!', post('code'));
        if (count($parts) != 2)
            throw new ValidationException(['code' => e(trans('rainlab.user::lang.account.invalid_activation_code'))]);

        list($userId, $code) = $parts;

        if (!strlen(trim($userId)) || !($user = Auth::findUserById($userId)))
            throw new ApplicationException(e(trans('rainlab.user::lang.account.invalid_user')));

        if (!$user->attemptResetPassword($code, post('password')))
            throw new ValidationException(['code' => e(trans('rainlab.user::lang.account.invalid_activation_code'))]);
    }

    /**
     * Returns the reset password code from the URL
     * @return string
     */
    public function code()
    {
        $routeParameter = $this->property('paramCode');
        return $this->param($routeParameter);
    }

}