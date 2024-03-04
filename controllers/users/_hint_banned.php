<div class="layout-row min-size">
    <div class="callout callout-danger">
        <div class="header">
            <i class="icon-ban"></i>
            <h3><?= __("User has been banned") ?></h3>
            <p>
                <?= __("This user has been banned by an administrator and will be unable to sign in.") ?>
                <a href="javascript:;"
                    data-request="onUnbanUser"
                    data-request-confirm="<?= __("Do you really want to unban this user?") ?>"
                    data-stripe-load-indicator
                ><?= __("Unban this user") ?></a>.
            </p>
        </div>
    </div>
</div>
