<?php namespace RainLab\User\Classes\ActionManager;

use Auth;
use Request;
use Validator;
use RainLab\User\Models\User;
use RainLab\User\Models\UserLog;
use RainLab\User\Helpers\User as UserHelper;
use ForbiddenException;

/**
 * ActionChangePassword
 *
 * @package rainlab\user
 * @author Alexey Bobkov, Samuel Georges
 */
trait ActionChangePassword
{
    /**
     * changePassword updates the password of the authenticated user, requiring
     * the current password for confirmation
     */
    public function changePassword(array $input): void
    {
        $user = $this->user();
        if (!$user) {
            throw new ForbiddenException;
        }

        $this->updateUserPassword($user, $input);

        UserLog::createRecord($user->getKey(), UserLog::TYPE_SELF_PASSWORD_CHANGE);

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
