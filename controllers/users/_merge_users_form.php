<?= Form::open(['id' => 'mergeUsersForm']) ?>
    <div class="modal-header flex-row-reverse">
        <button type="button" class="close" data-dismiss="popup">&times;</button>
        <h4 class="modal-title">
            <?= __("Merge Users") ?>
        </h4>
    </div>
    <div class="modal-body">
        <p>
            <?= __("Select the leading user who will keep their account. All records from the other selected users will be transferred to this user, and those users will be permanently deleted.") ?>
        </p>

        <div class="border rounded p-3 pb-1 mb-3">
            <div class="form-group radio-field pb-0">
                <label class="form-label">
                    <?= __("Leading User") ?>
                </label>
                <?php foreach ($mergeUsers as $user): ?>
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            id="mergeUser_<?= $user->id ?>"
                            name="leading_user_id"
                            value="<?= $user->id ?>"
                            type="radio"
                        />
                        <label class="form-check-label" for="mergeUser_<?= $user->id ?>">
                            <?= e($user->full_name) ?> - <?= e($user->email) ?>
                            <span class="text-muted">
                                #<?= $user->id ?>
                                <?php if ($user->is_guest): ?>
                                    (<?= __("Guest") ?>)
                                <?php endif ?>
                            </span>
                        </label>
                    </div>
                    <input type="hidden" name="checked[]" value="<?= $user->id ?>" />
                <?php endforeach ?>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button
            type="submit"
            class="btn btn-danger"
            data-popup-load-indicator
            data-request="onMergeUsers"
            data-request-confirm="<?= __('This will permanently delete the non-leading users and transfer all their records. Continue?') ?>">
            <?= __("Merge & Delete Users") ?>
        </button>
        <span class="button-separator">
            <?= __("or") ?>
        </span>
        <button type="button" class="btn btn-link text-muted" data-dismiss="popup">
            <?= __("Cancel") ?>
        </button>
    </div>
<?= Form::close() ?>
