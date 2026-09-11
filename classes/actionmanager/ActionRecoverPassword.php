<?php namespace RainLab\User\Classes\ActionManager;

use Validator;
use RainLab\User\Helpers\User as UserHelper;
use Illuminate\Contracts\Auth\PasswordBroker;
use ValidationException;

/**
 * ActionRecoverPassword triggers a request to recover the user password
 * using a password reset process
 *
 * @see \RainLab\User\Classes\ActionManager\ActionResetPassword
 *
 * @package rainlab\user
 * @author Alexey Bobkov, Samuel Georges
 */
trait ActionRecoverPassword
{
    /**
     * recoverPassword sends a password reset link to the supplied email address. Supported options:
     *
     * - resetUrl: an absolute URL to use in the reset email instead of the CMS entry point,
     *   the reset token and email address are appended as query parameters.
     */
    public function recoverPassword(array $input, array $options = []): void
    {
        Validator::make($input, [
            'email' => 'required|email'
        ])->validate();

        $callback = null;
        if ($resetUrl = array_get($options, 'resetUrl')) {
            $callback = function($user, $token) use ($resetUrl) {
                $user->setUrlForPasswordReset($resetUrl);
                $user->sendPasswordResetNotification($token);
            };
        }

        $status = $this->makePasswordBroker()->sendResetLink(
            array_only($input, ['email']),
            $callback
        );

        if ($status === PasswordBroker::RESET_THROTTLED) {
            throw new ValidationException([UserHelper::username() => __("Please wait before retrying.")]);
        }

        if ($status !== PasswordBroker::RESET_LINK_SENT) {
            throw new ValidationException([UserHelper::username() => __("We can't find a user with that email address.")]);
        }
    }
}
