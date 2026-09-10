<div class="layout-row min-size">
    <div class="callout callout-warning">
        <div class="header">
            <i class="icon-warning"></i>
            <h3><?= __("User awaiting approval!") ?></h3>
            <p>
                <?= __("This user has not been approved by an administrator.") ?>
                <a href="javascript:;"
                    data-request="onApproveUser"
                    data-request-confirm="<?= __("Do you really want to approve this user?") ?>"
                    data-stripe-load-indicator
                ><?= __("Approve this user manually") ?></a>.
            </p>
        </div>
    </div>
</div>
