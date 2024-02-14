<a
    href="<?= Backend::url('rainlab/user/users') ?>"
    class="btn btn-default oc-icon-chevron-left">
    <?= __("Back to users list") ?>
</a>
<a
    href="<?= Backend::url('rainlab/user/users/update/'.$formModel->id) ?>"
    class="btn btn-primary oc-icon-pencil">
    <?= __("Update details") ?>
</a>
<?php if ($this->user->hasAccess('rainlab.users.impersonate_user')): ?>
    <a
        href="javascript:;"
        data-request="onImpersonateUser"
        data-request-confirm="<?= __("Impersonate this user? You can revert to your original state by logging out.") ?>"
        class="btn btn-default oc-icon-user-secret">
        <?= __("Impersonate user") ?>
    </a>
<?php endif ?>

<?php
/* @todo
<div class="btn-group">
    <a
        href="<?= Backend::url('rainlab/user/users/update/'.$formModel->id) ?>"
        class="btn btn-default oc-icon-pencil">
        Deactivate
    </a>
    <a
        href="<?= Backend::url('rainlab/user/users/update/'.$formModel->id) ?>"
        class="btn btn-default oc-icon-pencil">
        Ban user
    </a>
    <a
        href="<?= Backend::url('rainlab/user/users/update/'.$formModel->id) ?>"
        class="btn btn-default oc-icon-pencil">
        Delete
    </a>
</div>
*/
?>

<?=
    /**
     * @event rainlab.user.view.extendPreviewToolbar
     * Fires when preview user toolbar is rendered.
     *
     * Example usage:
     *
     *     Event::listen('rainlab.user.view.extendPreviewToolbar', function (
     *         (RainLab\User\Controllers\Users) $controller,
     *         (RainLab\User\Models\User) $record
     *     ) {
     *         return $controller->makePartial('~/path/to/partial');
     *     });
     *
     */
    $this->fireViewEvent('rainlab.user.view.extendPreviewToolbar', [
        'record' => $formModel
    ]);
?>
