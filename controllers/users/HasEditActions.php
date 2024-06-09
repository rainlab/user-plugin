<?php namespace RainLab\User\Controllers\Users;

use Auth;
use Lang;
use Flash;
use Response;
use RainLab\User\Models\UserGroup;

/**
 * HasEditActions
 */
trait HasEditActions
{
    /**
     * Manually activate a user
     */
    public function preview_onActivate($recordId = null)
    {
        $model = $this->formFindModelObject($recordId);

        $model->markEmailAsVerified();

        Flash::success(__("User has been activated"));

        if ($redirect = $this->makeRedirect('update-close', $model)) {
            return $redirect;
        }
    }

    /**
     * preview_onBan manually bans a user
     */
    public function preview_onBanUser($recordId = null)
    {
        $model = $this->formFindModelObject($recordId);

        $model->ban();

        Flash::success(__("User has been banned"));

        if ($redirect = $this->makeRedirect('update-close', $model)) {
            return $redirect;
        }
    }

    /**
     * preview_onUnban manually unbans a user
     */
    public function preview_onUnbanUser($recordId = null)
    {
        $model = $this->formFindModelObject($recordId);

        $model->unban();

        Flash::success(__("User has been unbanned"));

        if ($redirect = $this->makeRedirect('update-close', $model)) {
            return $redirect;
        }
    }

    /**
     * Display the convert to registered user popup
     */
    public function preview_onLoadConvertGuestForm($recordId)
    {
        $this->vars['groups'] = UserGroup::withoutGuest()->get();

        return $this->makePartial('convert_guest_form');
    }

    /**
     * Manually convert a guest user to a registered one
     */
    public function preview_onConvertGuest($recordId)
    {
        $model = $this->formFindModelObject($recordId);

        // Convert user and send notification
        $model->convertToRegistered(post('send_registration_notification', false));

        // Add user to new group, or remove from guest group
        if (($groupId = post('new_group')) && ($group = UserGroup::find($groupId))) {
            $model->primary_group = $group;
            $model->save();
        }

        Flash::success(__("User has been converted to a registered account"));

        if ($redirect = $this->makeRedirect('update-close', $model)) {
            return $redirect;
        }
    }

    /**
     * Impersonate this user
     */
    public function preview_onImpersonateUser($recordId)
    {
        if (!$this->user->hasAccess('rainlab.users.impersonate_user')) {
            return Response::make(Lang::get('backend::lang.page.access_denied.label'), 403);
        }

        $model = $this->formFindModelObject($recordId);

        Auth::impersonate($model);

        Flash::success(__("You are now impersonating this user"));
    }

    /**
     * preview_onRestoreUser restores a user
     */
    public function preview_onRestoreUser($recordId = null)
    {
        $model = $this->formFindModelObject($recordId);

        $model->restore();

        Flash::success(__("Restored the selected users"));

        if ($redirect = $this->makeRedirect('delete', $model)) {
            return $redirect;
        }
    }

    /**
     * update_onDelete
     */
    public function update_onDelete($recordId = null)
    {
        return $this->preview_onDelete($recordId);
    }

    /**
     * update_onDelete force deletes a user.
     */
    public function preview_onDelete($recordId = null)
    {
        $model = $this->formFindModelObject($recordId);

        $model->smartDelete();

        Flash::success(__("Deleted the selected users"));

        if ($redirect = $this->makeRedirect('delete', $model)) {
            return $redirect;
        }
    }
}
