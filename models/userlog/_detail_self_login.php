<?php if ($record->is_two_factor): ?>
    <?= __(":name authenticated using 2FA successfully", ['name' => $record->actor_user_name_linked]) ?>
<?php else: ?>
    <?= __(":name authenticated successfully", ['name' => $record->actor_user_name_linked]) ?>
<?php endif ?>
