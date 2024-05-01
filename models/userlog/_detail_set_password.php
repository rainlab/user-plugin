<?php if ($record->is_system): ?>
    <?= __(":name changed this user's password", [
        'name' => e($record->actor_admin_name),
    ]) ?>
<?php else: ?>
    <?= __(":name changed their password", [
        'name' => e($record->actor_user_name)
    ]) ?>
<?php endif ?>
