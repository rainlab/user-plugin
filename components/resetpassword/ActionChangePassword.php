<?php namespace RainLab\User\Components\ResetPassword;

use Auth;
use Request;
use Validator;
use ForbiddenException;
use RainLab\User\Models\User;
use RainLab\User\Helpers\User as UserHelper;

/**
 * ActionChangePassword
 *
 * @package rainlab\user
 * @author Alexey Bobkov, Samuel Georges
 */
trait ActionChangePassword
{
    /**
     * actionChangePassword
     */
    protected function actionChangePassword()
    {
        $user = Auth::user();
        if (!$user) {
            throw new ForbiddenException;
        }

        $this->updateUserPassword($user, post());

        if (Request::hasSession()) {
            Request::session()->put([
                'password_hash_'.Auth::getDefaultDriver() => $user->getAuthPassword(),
            ]);
        }
    }

    /**
     * updateUserPassword
     */
    protected function updateUserPassword(User $user, array $input)
    {
        Validator::make($input, [
            'current_password' => ['required', 'string', 'current_password:web'],
            'password' => UserHelper::passwordRules(),
        ], [
            'current_password.current_password' => __("The provided password does not match your current password."),
        ])->validate();

        $user->password = $input['password'];
        $user->password_confirmation = $input['password_confirmation'] ?? null;
        $user->save();
    }
}
