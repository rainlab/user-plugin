<?php namespace RainLab\User\Classes\ActionManager;

use Auth;
use RainLab\User\Models\User;
use RainLab\User\Models\UserLog;
use ApplicationException;
use ForbiddenException;

/**
 * ActionVerifyEmail
 *
 * @package rainlab\user
 * @author Alexey Bobkov, Samuel Georges
 */
trait ActionVerifyEmail
{
    /**
     * sendVerifyEmail sends a verification email to the authenticated user. Supported options:
     *
     * - verifyUrl: an absolute URL to use in the verification email instead of the CMS entry point.
     */
    public function sendVerifyEmail(array $options = []): void
    {
        $user = $this->user();

        if (!$user) {
            throw new ForbiddenException;
        }

        $limiter = $this->makeVerifyRateLimiter();

        if ($limiter->tooManyAttempts(1)) {
            $seconds = $limiter->availableIn();

            throw new ApplicationException(__("Too many verification attempts. Please try again in :seconds seconds.", [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]));
        }

        $limiter->increment();

        if ($verifyUrl = array_get($options, 'verifyUrl')) {
            $user->setUrlForEmailVerification($verifyUrl);
        }

        $user->sendEmailVerificationNotification();
    }

    /**
     * confirmVerifiedEmail marks the user email address as verified using an
     * emailed verification code
     */
    public function confirmVerifiedEmail($verifyCode): void
    {
        // Locate user from bearer code
        $user = User::findUserForEmailVerification($verifyCode);
        if (!$user) {
            throw new ApplicationException(__('The provided email verification code was invalid.'));
        }

        // Ensure verification is for the logged in user
        if ($sessionUser = $this->user()) {
            $user = $sessionUser;
        }
        // Make the bearer available the current page cycle
        else {
            Auth::setUserViaBearerToken($user);
        }

        // Verify the bearer/user
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();

            UserLog::createRecord($user->getKey(), UserLog::TYPE_SELF_VERIFY, [
                'user_full_name' => $user->full_name,
                'user_email' => $user->email,
            ]);
        }
    }

    /**
     * makeVerifyRateLimiter
     */
    protected function makeVerifyRateLimiter()
    {
        return new \System\Classes\RateLimiter('verify:'.$this->user()->getKey());
    }
}
