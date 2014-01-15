<?php namespace Plugins\RainLab\User\Components;

use Auth;
use Mail;
use View;
use Validator;
use Modules\Cms\Classes\ComponentBase;
use October\Rain\Support\ValidationException;
use Modules\System\Classes\ApplicationException;

class ResetForm extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Reset Password',
            'description' => 'Forgotten password form.'
        ];
    }

    public function defineProperties()
    {
        return [
            'code-param' => [
                'title' => 'Code Parameter Name',
                'description' => 'The page URL parameter used for the activation code',
                'type' => 'string',
                'default' => 'code'
            ]
        ];
    }

    public function onRestorePassword()
    {
        $rules = [
            'email' => 'required|email|min:2|max:32'
        ];

        $validation = Validator::make(post(), $rules);
        if ($validation->fails())
            throw new ValidationException($validation);

        if (!($user = Auth::findUserByLogin(post('email'))))
            throw new ApplicationException('A user was not found with the given credentials.');

        $code = implode('!', [$user->id, $user->getResetPasswordCode()]);
        $link = $this->controller->currentPageUrl([
            $this->property('code-param') => $code
        ]);

        $data = [
            'name' => $user->name,
            'link' => $link,
            'code' => $code
        ];

        Mail::send(['text' => 'rainlab.user::emails.restore'], $data, function($message) use ($user)
        {
            $message->to($user->email, $user->full_name)->subject('Requested Password Reset');
        });
    }

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
            throw new ValidationException(['code' => 'Invalid activation code supplied']);

        list($userId, $code) = $parts;

        if (!strlen(trim($userId)) || !($user = Auth::findUserById($userId)))
            throw new ApplicationException('A user was not found with the given credentials.');

        if (!$user->attemptResetPassword($code, post('password')))
            throw new ValidationException(['code' => 'Invalid activation code supplied']);
    }

    public function getCode()
    {
        $routeParameter = $this->property('code-param');
        return $this->param($routeParameter);
    }

}