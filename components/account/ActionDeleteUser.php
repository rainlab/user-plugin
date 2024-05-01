<?php namespace RainLab\User\Components\Account;

use Auth;
use Request;
use RainLab\User\Models\User;
use RainLab\User\Models\UserLog;
use ValidationException;

/**
 * ActionDeleteUser
 *
 * @package rainlab\user
 * @author Alexey Bobkov, Samuel Georges
 */
trait ActionDeleteUser
{
    /**
     * actionDeleteUser
     */
    protected function actionDeleteUser()
    {
        if (!$this->isUserPasswordValid(post('password'))) {
            throw new ValidationException([
                'password' => __('This password does not match our records.'),
            ]);
        }

        $this->deleteUser($this->user()->fresh());

        Auth::logout();
        Request::session()->invalidate();
        Request::session()->regenerateToken();
    }

    /**
     * deleteUser
     */
    protected function deleteUser(User $user)
    {
        UserLog::createRecord($user->getKey(), UserLog::TYPE_SELF_DELETE, [
            'user_full_name' => $user->full_name,
        ]);

        $user->smartDelete();
    }
}
