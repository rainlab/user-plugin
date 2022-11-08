<?php namespace RainLab\User\Components;

use Auth;
use Lang;
use Mail;
use Validator;
use ValidationException;
use ApplicationException;
use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use RainLab\User\Models\User as UserModel;

/**
 * ResetPassword controls the password reset workflow
 *
 * When a user has forgotten their password, they are able to reset it using
 * a unique token that, sent to their email address upon request.
 */
class ResetPassword extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => /*Reset Password*/'rainlab.user::lang.reset_password.reset_password',
            'description' => /*Forgotten password form.*/'rainlab.user::lang.reset_password.reset_password_desc'
        ];
    }

    public function defineProperties()
    {
        return [
            'paramCode' => [
                'title'       => /*Reset Code Param*/'rainlab.user::lang.reset_password.code_param',
                'description' => /*The page URL parameter used for the reset code*/'rainlab.user::lang.reset_password.code_param_desc',
                'type'        => 'string',
                'default'     => 'code'
            ],
            'resetPage' => [
                'title'       => /* Reset Page */'rainlab.user::lang.account.reset_page',
                'description' => /* Select a page to use for resetting the account password */'rainlab.user::lang.account.reset_page_comment',
                'type'        => 'dropdown',
                'default'     => ''
            ],
        ];
    }

    /**
     * getResetPageOptions
     */
    public function getResetPageOptions()
    {
        return [
            '' => '- current page -',
        ] + Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    //
    // Properties
    //

    /**
     * Returns the reset password code from the URL
     * @return string
     */
    public function code()
    {
        $routeParameter = $this->property('paramCode');

        if ($code = $this->param($routeParameter)) {
            return $code;
        }

        return get('reset');
    }

    //
    // AJAX
    //

    /**
     * Trigger the password reset email
     */
    public function onRestorePassword()
    {
        $rules = [
            'email' => 'required|email|between:6,255'
        ];

        $validation = Validator::make(post(), $rules);
        if ($validation->fails()) {
            throw new ValidationException($validation);
        }

        $user = Auth::findUserByEmail(post('email'));
        if (!$user || $user->is_guest || $user->trashed()) {
            throw new ApplicationException(Lang::get(/*A user was not found with the given credentials.*/'rainlab.user::lang.account.invalid_user'));
        }

        $code = implode('!', [$user->id, $user->getResetPasswordCode()]);

        $link = $this->makeResetUrl($code);

        $data = [
            'name' => $user->name,
            'username' => $user->username,
            'link' => $link,
            'code' => $code
        ];

        Mail::send('rainlab.user::mail.restore', $data, function($message) use ($user) {
            $message->to($user->email, $user->full_name);
        });
    }

    /**
     * Perform the password reset
     */
    public function onResetPassword()
    {
        $rules = [
            'code'     => 'required',
            'password' => 'required|between:' . UserModel::getMinPasswordLength() . ',255'
        ];

        $validation = Validator::make(post(), $rules);
        if ($validation->fails()) {
            throw new ValidationException($validation);
        }

        $errorFields = ['code' => Lang::get(/*Invalid activation code supplied.*/'rainlab.user::lang.account.invalid_activation_code')];

        /*
         * Break up the code parts
         */
        $parts = explode('!', post('code'));
        if (count($parts) != 2) {
            throw new ValidationException($errorFields);
        }

        list($userId, $code) = $parts;

        if (!strlen(trim($userId)) || !strlen(trim($code)) || !$code) {
            throw new ValidationException($errorFields);
        }

        if (!$user = Auth::findUserById($userId)) {
            throw new ValidationException($errorFields);
        }

        if (!$user->attemptResetPassword($code, post('password'))) {
            throw new ValidationException($errorFields);
        }

        // Check needed for compatibility with legacy systems
        if (method_exists(\RainLab\User\Classes\AuthManager::class, 'clearThrottleForUserId')) {
            Auth::clearThrottleForUserId($user->id);
        }
    }

    //
    // Helpers
    //

    /**
     * makeResetUrl returns a link used to reset the user account.
     * @return string
     */
    protected function makeResetUrl($code)
    {
        $params = [
            $this->property('paramCode') => $code
        ];

        // Locate the current page
        $url = '';

        if ($pageName = $this->property('resetPage')) {
            $url = $this->pageUrl($pageName, $params);
        }

        if (!$url) {
            $url = $this->currentPageUrl($params);
        }

        if (strpos($url, $code) === false) {
            $url .= '?reset=' . $code;
        }

        return $url;
    }
}
