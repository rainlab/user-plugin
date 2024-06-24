<?php namespace RainLab\User\Components\Account;

use Flash;
use Request;
use Redirect;
use RainLab\User\Models\User;
use RainLab\User\Models\UserLog;
use ApplicationException;

/**
 * ActionVerifyEmail
 *
 * @package rainlab\user
 * @author Alexey Bobkov, Samuel Georges
 */
trait ActionVerifyEmail
{
    /**
     * actionVerifyEmail
     */
    protected function actionVerifyEmail()
    {
        $user = $this->user();
        if (!$user) {
            throw new ApplicationException(__("User not found"));
        }

        $limiter = $this->makeVerifyRateLimiter();

        if ($limiter->tooManyAttempts(2)) {
            $seconds = $limiter->availableIn();

            throw new ApplicationException(__("Too many verification attempts. Please try again in :seconds seconds.", [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]));
        }

        $limiter->increment();

        $user->sendEmailVerificationNotification();
    }

    /**
     * actionConfirmEmail
     */
    protected function actionConfirmEmail($verifyCode = null)
    {
        if ($verifyCode === null) {
            $verifyCode = post('verify');
        }

        $user = User::findUserForEmailVerification($verifyCode);
        if (!$user) {
            throw new ApplicationException(__('The provided email verification code was invalid.'));
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();

            UserLog::createRecord($user->getKey(), UserLog::TYPE_SELF_VERIFY, [
                'user_full_name' => $user->full_name,
                'user_email' => $user->email,
            ]);
        }
    }

    /**
     * checkVerifyEmailRedirect
     */
    protected function checkVerifyEmailRedirect()
    {
        if ($verifyCode = get('verify')) {
            try {
                $this->actionConfirmEmail($verifyCode);
                Flash::success(__("Thank you for verifying your email."));
            }
            catch (ApplicationException $ex) {
                Flash::error($ex->getMessage());
            }

            return Redirect::to(Request::fullUrlWithoutQuery(['verify']));
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
