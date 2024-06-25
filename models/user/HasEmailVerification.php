<?php namespace RainLab\User\Models\User;

use Cms;
use Log;
use Str;
use Date;
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
     * @var string|null emailVerificationUrl
     */
    protected $emailVerificationUrl;

    /**
     * setUrlForEmailVerification
     */
    public function setUrlForEmailVerification(?string $url)
    {
        $this->emailVerificationUrl = $url;
    }

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
     * getCodeForVerification
     */
    public function getCodeForEmailVerification(): string
    {
        $activationCode = time().'x'.$this->id.'x'.Str::random(24);

        $this->forceFill([
            'activation_code' => $activationCode
        ]);

        $this
            ->newQuery()
            ->where('id', $this->id)
            ->update(['activation_code' => $activationCode])
        ;

        return $activationCode;
    }

    /**
     * findUserForEmailVerification checks a supplied verification code and returns the timestamp
     * it was created or null if the check fails
     */
    public static function findUserForEmailVerification($code): ?static
    {
        if (!is_string($code)) {
            return null;
        }

        $parts = explode("x", $code, 3);

        if (count($parts) !== 3) {
            return null;
        }

        $timestamp = intval($parts[0]);
        if (!$timestamp) {
            return null;
        }

        $expiration = Date::createFromTimestamp($timestamp)->addMinutes(Config::get('auth.verification.expire', 60));
        if ($expiration->isPast()) {
            return null;
        }

        $user = static::where('id', $parts[1])->where('activation_code', $code)->first();
        if (!$user) {
            return null;
        }

        // Extra redundancy check
        if (!$user->activation_code || $user->activation_code !== $code) {
            return null;
        }

        $user
            ->newQuery()
            ->where('id', $user->id)
            ->update(['activation_code' => null])
        ;

        return $user;
    }

    /**
     * sendEmailVerificationNotification sends the mail message used to verify an account
     */
    public function sendEmailVerificationNotification()
    {
        $expiration = Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60));

        $url = $this->emailVerificationUrl ?: Cms::entryUrl('account');
        $url .= str_contains($url, '?') ? '&' : '?';
        $url .= http_build_query([
            'verify' => $this->getCodeForEmailVerification()
        ]);

        $data = [
            'url' => $url,
            'expiration' => $expiration
        ];

        $data += $this->getNotificationVars();

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
                    Mail::send($setting->system_message_template, $notificationVars, function($message) use ($setting) {
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
