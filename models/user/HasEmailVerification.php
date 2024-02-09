<?php namespace RainLab\User\Models\User;

use Url;
use Cms;
use Mail;
use Config;
use Carbon\Carbon;

/**
 * HasEmailVerification contract
 */
trait HasEmailVerification
{
    /**
     * hasVerifiedEmail determines if the user has verified their email address
     */
    public function hasVerifiedEmail(): bool
    {
        return $this->activated_at !== null;
    }

    /**
     * markEmailAsVerified for the given user
     */
    public function markEmailAsVerified(): bool
    {
        $this->forceFill([
            'activated_at' => $this->freshTimestamp()
        ]);

        return $this->save();
    }

    /**
     * sendEmailVerificationNotification mail message
     */
    public function sendEmailVerificationNotification()
    {
        $expiration = Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60));

        $url = Cms::entryUrl('profileDetails', [
            'id' => $this->getKey(),
            'verify' => sha1($this->getEmailForVerification())
        ]);

        $url = Url::toSigned($url, $expiration);

        $data = [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'url' => $url
        ];

        Mail::send('user:verify_email', $data, function($message) {
            $message->to($this->email, $this->full_name);
        });
    }

    /**
     * getEmailForVerification
     */
    public function getEmailForVerification(): string
    {
        return $this->email;
    }
}
