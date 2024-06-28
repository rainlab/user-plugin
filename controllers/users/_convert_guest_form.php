<?= Form::open(['id' => 'convertGuestForm']) ?>
    <div class="modal-header flex-row-reverse">
        <button type="button" class="close" data-dismiss="popup">&times;</button>
        <h4 class="modal-title"><?= __("Convert Guest User") ?></h4>
    </div>
    <div class="modal-body">
        <?php if ($this->fatalError): ?>
            <p class="flash-message static error"><?= $fatalError ?></p>
        <?php endif ?>

        <div class="form-group dropdown-field span-full">
            <label class="form-label"><?= __("Select a New User Group") ?></label>
            <select class="form-control custom-select" name="new_group">
                <option selected="selected" value="">- <?= __("Use Default") ?> -</option>
                <?php foreach ($groups as $group): ?>
                    <option value="<?= $group->id ?>"><?= e($group->name) ?></option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="form-group checkbox-field span-full pb-3">
            <div class="checkbox custom-checkbox">
                <input type="hidden" class="checkbox" value="" name="send_registration_notification">
                <input name="send_registration_notification" value="1" type="checkbox" id="sendNotification" checked="checked" />
                <label class="storm-icon-pseudo" for="sendNotification"><?= __("Send Registration Notification") ?></label>
            </div>
            <p class="help-block form-text"><?= __("Use this checkbox to generate new random password for the user and send a registration notification email.") ?></p>
        </div>

    </div>

    <div class="modal-footer">
        <button
            type="submit"
            class="btn btn-primary"
            data-popup-load-indicator
            data-request="onConvertGuest">
            <?= __("Convert to Registered") ?>
        </button>
        <span class="button-separator"><?= __("or") ?></span>
        <button
            type="button"
            class="btn btn-link text-muted"
            data-dismiss="popup">
            <?= __("Cancel") ?>
        </button>
    </div>
<?= Form::close() ?>
