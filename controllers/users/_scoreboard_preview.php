<div data-control="toolbar">
    <?php if ($avatarUrl = $formModel->getAvatarThumb(144)): ?>
        <div class="scoreboard-item thumbnail-value me-3">
            <img
                src="<?= $avatarUrl ?>"
                class="img-thumbnail object-fit-cover"
                alt="<?= e($formModel->full_name) ?>"
                width="72"
                height="72"
            />
        </div>
    <?php endif ?>

    <div class="scoreboard-item title-value">
        <h4><?= __("User") ?></h4>
        <?php if ($formModel->full_name): ?>
            <p><?= e($formModel->full_name) ?></p>
        <?php else: ?>
            <p><em><?= __("Anonymous") ?></em></p>
        <?php endif ?>
        <p class="description">
            <?= __("Email") ?>: <?= Html::mailto($formModel->email) ?>
        </p>
    </div>

    <div class="scoreboard-item title-value">
        <h4><?= __("Type") ?></h4>
        <p><?= $formModel->is_guest ? __("Guest") : __("Registered") ?></p>
        <p class="description">
            <?= __("Created") ?>: <?= $formModel->created_at->toFormattedDateString() ?>
        </p>
    </div>

    <?php if ($formModel->last_seen): ?>
        <div class="scoreboard-item title-value">
            <h4><?= __("Last Seen") ?></h4>
            <p><?= $formModel->last_seen->diffForHumans() ?></p>
            <p class="description">
                <?= $formModel->isOnline() ? __("Online now") : __("Currently offline") ?>
            </p>
        </div>
    <?php endif ?>

    <?php if ($formModel->deleted_at): ?>
        <div class="scoreboard-item title-value">
            <h4><?= __("Marked as Deleted") ?></h4>
            <p><?= $formModel->deleted_at->toFormattedDateString() ?></p>
            <p class="description"><a
                href="javascript:;"
                data-request="onRestoreUser"
                data-request-confirm="<?= __("Are you sure?") ?>"
                ><?= __("Restore Account") ?>
            </a></p>
        </div>
    <?php endif ?>
</div>
