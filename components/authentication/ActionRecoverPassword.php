<?php namespace RainLab\User\Components\Authentication;

use App;
use Cms;
use Request;
use Redirect;
use RainLab\User\Helpers\User as UserHelper;
use Illuminate\Contracts\Auth\PasswordBroker;
use ValidationException;

/**
 * ActionRecoverPassword triggers a request to recover the user password
 * using a password reset process in the Password component
 *
 * @see RainLab\User\Components\ResetPassword
 *
 * @package rainlab\user
 * @author Alexey Bobkov, Samuel Georges
 */
trait ActionRecoverPassword
{
    /**
     * actionRecoverPassword
     */
    protected function actionRecoverPassword()
    {
        Request::validate(['email' => 'required|email']);

        $status = $this->makePasswordBroker()->sendResetLink([
            'email' => post('email')
        ]);

        if ($status === PasswordBroker::RESET_THROTTLED) {
            throw new ValidationException([UserHelper::username() => __("Please wait before retrying.")]);
        }

        if ($status !== PasswordBroker::RESET_LINK_SENT) {
            throw new ValidationException([UserHelper::username() => __("We can't find a user with that email address.")]);
        }
    }

    /**
     * makePasswordBroker to be used during password reset.
     */
    protected function makePasswordBroker(): PasswordBroker
    {
        return App::make('auth.password')->broker('users');
    }
}
