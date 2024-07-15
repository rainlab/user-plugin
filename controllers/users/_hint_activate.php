<div class="layout-row min-size">
    <div class="callout callout-warning">
        <div class="header">
            <i class="icon-warning"></i>
            <h3><?= __("User not activated!") ?></h3>
            <p>
                <?= __("This user has not confirmed their email address.") ?>
                <a href="javascript:;"
                    data-request="onActivate"
                    data-request-confirm="<?= __("Do you really want to activate this user?") ?>"
                    data-stripe-load-indicator
                ><?= __("Activate this user manually") ?></a>.
            </p>
        </div>
    </div>
</div>
