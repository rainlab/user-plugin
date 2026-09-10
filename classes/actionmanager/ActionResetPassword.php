<?php namespace RainLab\User\Classes\ActionManager;

use Str;
use Validator;
use RainLab\User\Models\User;
use RainLab\User\Models\UserLog;
use RainLab\User\Helpers\User as UserHelper;
use Illuminate\Contracts\Auth\PasswordBroker;
use ValidationException;

/**
 * ActionResetPassword
 *
 * @package rainlab\user
 * @author Alexey Bobkov, Samuel Georges
 */
trait ActionResetPassword
{
    /**
     * resetPassword sets a new password using a reset token sent by email
     */
    public function resetPassword(array $input): void
    {
        Validator::make($input, [
            'token' => 'required',
            'email' => ['required', 'email'],
            'password' => 'required',
        ])->validate();

        $status = $this->makePasswordBroker()->reset(array_only($input, [
            'email', 'password', 'password_confirmation', 'token'
        ]), function($user) use ($input) {
            $this->resetUserPassword($user, $input);
            $this->completePasswordReset($user);
        });

        if ($status === PasswordBroker::RESET_THROTTLED) {
            throw new ValidationException(['password' => __("Please wait before retrying.")]);
        }

        if ($status !== PasswordBroker::PASSWORD_RESET) {
            throw new ValidationException(['password' => __("This password reset token is invalid. Please try recovering your password again.")]);
        }
    }

    /**
     * resetUserPassword updates the user password
     */
    protected function resetUserPassword(User $user, array $input): void
    {
        Validator::make($input, [
            'password' => UserHelper::passwordRules(),
        ])->validate();

        $user->forceFill([
            'password' => $input['password'],
        ])->save();
    }

    /**
     * completePasswordReset
     */
    protected function completePasswordReset(User $user)
    {
        $user->setRememberToken(Str::random(60));

        $user->save();

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        /**
         * @event rainlab.user.passwordReset
         * Provides custom logic for resetting a user password.
         *
         * Example usage:
         *
         *     Event::listen('rainlab.user.passwordReset', function ($component, $user) {
         *         // Fire logic
         *     });
         *
         * Or
         *
         *     $component->bindEvent('user.passwordReset', function ($user) {
         *         // Fire logic
         *     });
         *
         */
        $this->fireSystemEvent('rainlab.user.passwordReset', [$user]);

        UserLog::createRecord($user->getKey(), UserLog::TYPE_SELF_PASSWORD_RESET);
    }
}
