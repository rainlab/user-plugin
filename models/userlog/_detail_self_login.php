<?php if ($record->is_two_factor): ?>
    <?= __(":name authenticated using 2FA successfully", ['name' => e($record->actor_user_name)]) ?>
<?php else: ?>
    <?= __(":name authenticated successfully", ['name' => e($record->actor_user_name)]) ?>
<?php endif ?>
