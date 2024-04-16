<?php namespace RainLab\User\Models\User;

use Url;
use Cms;
use Log;
use Mail;
use Event;
use Config;
use System\Models\MailTemplate;
use RainLab\User\Models\Setting as UserSetting;
use Carbon\Carbon;
use Exception;

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

        $this->sendEmailConfirmationNotification();

        Event::fire('rainlab.user.activate', [$this]);

        return $this->save();
    }

    /**
     * getEmailForVerification
     */
    public function getEmailForVerification(): string
    {
        return $this->email;
    }

    /**
     * sendEmailVerificationNotification sends the mail message used to verify an account
     */
    public function sendEmailVerificationNotification()
    {
        $expiration = Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60));

        $url = Cms::entryUrl('account') . '?' . http_build_query([
            'id' => $this->getKey(),
            'verify' => sha1($this->getEmailForVerification())
        ]);

        $url = Url::toSigned($url, $expiration);

        $data = $this->getNotificationVars() + [
            'url' => $url
        ];

        Mail::send('user:verify_email', $data, function($message) {
            $message->to($this->email, $this->full_name);
        });
    }

    /**
     * sendEmailConfirmationNotification sends a mail message when the user is verified
     */
    public function sendEmailConfirmationNotification()
    {
        $setting = UserSetting::instance();
        $notificationVars = $this->getNotificationVars();

        // Notify user
        if ($setting->notify_user) {
            if (MailTemplate::canSendTemplate($setting->user_message_template)) {
                try {
                    Mail::send($setting->user_message_template, $notificationVars, function($message) {
                        $message->to($this->email, $this->full_name);
                    });
                }
                catch (Exception $ex) {
                    Log::error($ex);
                }
            }
        }

        // Notify admins
        if ($setting->notify_system && $setting->admin_group) {
            if (MailTemplate::canSendTemplate($setting->system_message_template)) {
                try {
                    Mail::sendTo($setting->system_message_template, $notificationVars, function($message) use ($setting) {
                        foreach ($setting->admin_group->users as $admin) {
                            $message->to($admin->email, $admin->full_name);
                        }
                    });
                }
                catch (Exception $ex) {
                    Log::error($ex);
                }
            }
        }
    }
}
