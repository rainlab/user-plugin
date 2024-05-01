<?php if ($record->is_system): ?>
    <?= __(":name created this user", ['name' => e($record->actor_admin_name)]) ?>
<?php else: ?>
    <?= __(":name has registered as a new user", ['name' => e($record->actor_user_name)]) ?>
<?php endif ?>
