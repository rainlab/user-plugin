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
 * @package october\user
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

        $status = $this->makePasswordBroker()->reset(array_only(input(), [
            'email', 'password', 'password_confirmation', 'token'
        ]), function($user) {
            $this->resetUserPassword($user, input());
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
         * @event user.password.reset
         * Provides custom logic for resetting a user password.
         *
         * Example usage:
         *
         *     Event::listen('user.password.reset', function ($user) {
         *         // Fire logic
         *     });
         *
         * Or
         *
         *     $component->bindEvent('password.reset', function ($user) {
         *         // Fire logic
         *     });
         *
         */
        $this->fireSystemEvent('user.password.reset', [$user]);
    }

    /**
     * makePasswordBroker to be used during password reset.
     */
    protected function makePasswordBroker(): PasswordBroker
    {
        return App::make('auth.password')->broker('users');
    }
}
