<?php namespace RainLab\User\Components\ResetPassword;

use App;
use Str;
use Request;
use Validator;
use RainLab\User\Models\User;
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
     * actionResetPassword
     */
    protected function actionResetPassword()
    {
        Request::validate([
            'token' => 'required',
            'email' => ['required', 'email'],
            'password' => 'required',
        ]);

        $status = $this->makePasswordBroker()->reset(array_only(post(), [
            'email', 'password', 'password_confirmation', 'token'
        ]), function($user) {
            $this->resetUserPassword($user, post());
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
    }

    /**
     * makePasswordBroker to be used during password reset.
     */
    protected function makePasswordBroker(): PasswordBroker
    {
        return App::make('auth.password')->broker('users');
    }
}
