<?php namespace RainLab\User\Controllers\Users;

use Flash;
use RainLab\User\Models\User;
use RainLab\User\Models\UserLog;
use ApplicationException;
use Exception;

/**
 * HasBulkActions
 */
trait HasBulkActions
{
    /**
     * onDeleteSelected
     */
    public function onDeleteSelected()
    {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {
            foreach ($checkedIds as $objectId) {
                try {
                    if ($object = User::withTrashed()->find($objectId)) {
                        UserLog::createSystemRecord($object->getKey(), UserLog::TYPE_ADMIN_DELETE);
                        $object->smartDelete();
                    }
                }
                catch (Exception $ex) {
                    Flash::error(__("Error with user :id - :message", ['id' => $object->email, 'message' => $ex->getMessage()]));
                    return $this->listRefresh();
                }
            }
        }

        Flash::success(__("Deleted the selected users"));
        return $this->listRefresh();
    }

    /**
     * onRestoreSelected
     */
    public function onRestoreSelected()
    {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {
            foreach ($checkedIds as $objectId) {
                try {
                    if ($object = User::withTrashed()->find($objectId)) {
                        $object->restore();
                        UserLog::createSystemRecord($object->getKey(), UserLog::TYPE_ADMIN_RESTORE);
                    }
                }
                catch (Exception $ex) {
                    Flash::error(__("Error with user :id - :message", ['id' => $object->email, 'message' => $ex->getMessage()]));
                    return $this->listRefresh();
                }
            }
        }

        Flash::success(__("Restored the selected users"));
        return $this->listRefresh();
    }

    /**
     * onActivateSelected
     */
    public function onActivateSelected()
    {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {
            foreach ($checkedIds as $objectId) {
                try {
                    if ($object = User::withTrashed()->find($objectId)) {
                        $object->markEmailAsVerified();
                    }
                }
                catch (Exception $ex) {
                    Flash::error(__("Error with user :id - :message", ['id' => $object->email, 'message' => $ex->getMessage()]));
                    return $this->listRefresh();
                }
            }
        }

        Flash::success(__("Activated the selected users"));
        return $this->listRefresh();
    }

    /**
     * onBanSelected
     */
    public function onBanSelected()
    {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {
            foreach ($checkedIds as $objectId) {
                try {
                    if ($object = User::withTrashed()->find($objectId)) {
                        $object->ban();
                        UserLog::createSystemRecord($object->getKey(), UserLog::TYPE_ADMIN_BAN);
                    }
                }
                catch (Exception $ex) {
                    Flash::error(__("Error with user :id - :message", ['id' => $object->email, 'message' => $ex->getMessage()]));
                    return $this->listRefresh();
                }
            }
        }

        Flash::success(__("Banned the selected users"));
        return $this->listRefresh();
    }

    /**
     * onUnbanSelected
     */
    public function onUnbanSelected()
    {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {
            foreach ($checkedIds as $objectId) {
                try {
                    if ($object = User::withTrashed()->find($objectId)) {
                        $object->unban();
                        UserLog::createSystemRecord($object->getKey(), UserLog::TYPE_ADMIN_UNBAN);
                    }
                }
                catch (Exception $ex) {
                    Flash::error(__("Error with user :id - :message", ['id' => $object->email, 'message' => $ex->getMessage()]));
                    return $this->listRefresh();
                }
            }
        }

        Flash::success(__("Unbanned the selected users"));
        return $this->listRefresh();
    }

    /**
     * onLoadMergeUsersForm shows the merge users popup with leader selection
     */
    public function onLoadMergeUsersForm()
    {
        $checkedIds = post('checked');

        if (!is_array($checkedIds) || count($checkedIds) < 2) {
            throw new ApplicationException(__("Please select at least two users to merge."));
        }

        $this->vars['mergeUsers'] = User::withTrashed()->whereIn('id', $checkedIds)->get();

        return $this->makePartial('merge_users_form');
    }

    /**
     * onMergeUsers merges selected users into the chosen leader
     */
    public function onMergeUsers()
    {
        $checkedIds = post('checked');
        $leadingUserId = post('leading_user_id');

        if (!$leadingUserId) {
            throw new ApplicationException(__("Please select a leading user."));
        }

        $leadingUser = User::withTrashed()->findOrFail($leadingUserId);

        foreach ((array) $checkedIds as $userId) {
            if ($userId == $leadingUserId) {
                continue;
            }

            if ($mergedUser = User::withTrashed()->find($userId)) {
                $leadingUser->mergeUser($mergedUser);
            }
        }

        Flash::success(__("Users have been merged successfully"));

        return $this->listRefresh();
    }
}
