<?php if ($record->is_system): ?>
    <?= __(":name created :user", ['name' => $record->actor_admin_name_linked, 'user' => $record->actor_user_name_linked]) ?>
<?php else: ?>
    <?= __(":name has registered as a new user", ['name' => $record->actor_user_name_linked]) ?>
<?php endif ?>
