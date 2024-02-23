<div class="scoreboard-item title-value">
    <h4><?= __("User") ?></h4>
    <?php if ($formModel->name): ?>
        <p><?= e($formModel->name) ?></p>
    <?php else: ?>
        <p><em><?= __("Anonymous") ?></em></p>
    <?php endif ?>
    <p class="description">
        <a href="mailto:<?= e($formModel->email) ?>">
            <?= e($formModel->email) ?>
        </a>
    </p>
</div>
<?php if ($formModel->created_at): ?>
    <div class="scoreboard-item title-value">
        <h4><?= __("Joined") ?></h4>
        <p title="<?= $formModel->created_at->diffForHumans() ?>">
            <?= $formModel->created_at->toFormattedDateString() ?>
        </p>
        <p class="description">
            <?= __("Status") ?>
            <?php if ($formModel->is_guest): ?>
                <?= __("Guest") ?>
            <?php elseif ($formModel->is_activated): ?>
                <?= __("Activated") ?>
            <?php else: ?>
                <?= __("Registered") ?>
            <?php endif ?>
        </p>
    </div>
<?php endif ?>
<?php if ($formModel->last_seen): ?>
    <div class="scoreboard-item title-value">
        <h4><?= __("Last Seen") ?></h4>
        <p><?= $formModel->last_seen->diffForHumans() ?></p>
        <p class="description">
            <?= $formModel->isOnline() ? __("Online now") : __("Currently offline") ?>
        </p>
    </div>
<?php endif ?>
