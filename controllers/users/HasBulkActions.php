<?php namespace RainLab\User\Controllers\Users;

use Flash;
use RainLab\User\Models\User;
use RainLab\User\Models\UserLog;
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
}
