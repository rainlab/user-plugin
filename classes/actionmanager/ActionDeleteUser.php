<?php namespace RainLab\User\Classes\ActionManager;

use Auth;
use Request;
use RainLab\User\Models\User;
use RainLab\User\Models\UserLog;
use ValidationException;
use ForbiddenException;

/**
 * ActionDeleteUser
 *
 * @package rainlab\user
 * @author Alexey Bobkov, Samuel Georges
 */
trait ActionDeleteUser
{
    /**
     * deleteUser removes the authenticated user from the system, requiring
     * their password for confirmation
     */
    public function deleteUser(array $input): void
    {
        if (!$this->user()) {
            throw new ForbiddenException;
        }

        if (!$this->isUserPasswordValid((string) array_get($input, 'password'))) {
            throw new ValidationException([
                'password' => __('This password does not match our records.'),
            ]);
        }

        $this->deleteUserRecord($this->user()->fresh());

        Auth::logout();

        if (Request::hasSession()) {
            Request::session()->invalidate();
            Request::session()->regenerateToken();
        }
    }

    /**
     * deleteUserRecord
     */
    protected function deleteUserRecord(User $user)
    {
        UserLog::createRecord($user->getKey(), UserLog::TYPE_SELF_DELETE, [
            'user_full_name' => $user->full_name,
        ]);

        $user->smartDelete();
    }
}
