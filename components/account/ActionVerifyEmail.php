<?php namespace RainLab\User\Components\Account;

use Cms;
use Auth;
use Flash;
use Request;
use Redirect;
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
     * actionVerifyEmail
     */
    protected function actionVerifyEmail()
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
     * checkVerifyEmailRedirect
     */
    protected function checkVerifyEmailRedirect()
    {
        $verifyCode = get('verify');
        if (!$verifyCode) {
            return;
        }

        try {
            $this->actionConfirmEmail($verifyCode);

            if ($flash = Cms::flashFromPost(__("Thank you for verifying your email."))) {
                Flash::success($flash);
            }
        }
        catch (ApplicationException $ex) {
            Flash::error($ex->getMessage());
        }

        if (in_array(get('redirect'), ['0', 'false'])) {
            return;
        }

        $redirectUrl = rtrim(Request::fullUrlWithQuery(['verify' => null]), '?');
        return Redirect::to($redirectUrl);
    }

    /**
     * makeVerifyRateLimiter
     */
    protected function makeVerifyRateLimiter()
    {
        return new \System\Classes\RateLimiter('verify:'.$this->user()->getKey());
    }
}
