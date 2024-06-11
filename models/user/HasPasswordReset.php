<?php namespace RainLab\User\Models\User;

use Cms;
use Mail;
use Config;
use Password;

/**
 * HasPasswordReset contract
 */
trait HasPasswordReset
{
    /**
     * getEmailForPasswordReset
     * @return string
     */
    public function getEmailForPasswordReset()
    {
        return $this->email;
    }

    /**
     * sendPasswordResetNotification
     * @param  string  $token
     */
    public function sendPasswordResetNotification($token)
    {
        $url = Cms::entryUrl('resetPassword') . '?' . http_build_query([
            'reset' => $token,
            'email' => $this->getEmailForPasswordReset()
        ]);

        $data = [
            'url' => $url,
            'token' => $token,
            'count' => Config::get('auth.passwords.users.expire')
        ];

        $data += $this->getNotificationVars();

        Mail::send('user:recover_password', $data, function($message) {
            $message->to($this->email, $this->full_name);
        });
    }

    /**
     * sendConfirmRegistrationNotification welcomes a new user an invites them to set a password
     */
    public function sendConfirmRegistrationNotification()
    {
        $token = Password::createToken($this);

        $url = Cms::entryUrl('resetPassword') . '?' . http_build_query([
            'reset' => $token,
            'email' => $this->getEmailForPasswordReset(),
            'new' => true
        ]);

        $data = [
            'url' => $url,
            'token' => $token,
            'count' => Config::get('auth.passwords.users.expire')
        ];

        $data += $this->getNotificationVars();

        Mail::send('user:invite_email', $data, function($message) {
            $message->to($this->email, $this->full_name);
        });
    }
}
