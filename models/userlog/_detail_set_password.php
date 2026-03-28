<?php if ($record->is_system): ?>
    <?= __(":name changed :user's password", [
        'name' => $record->actor_admin_name_linked,
        'user' => $record->actor_user_name_linked,
    ]) ?>
<?php else: ?>
    <?= __(":name changed their password", [
        'name' => $record->actor_user_name_linked,
    ]) ?>
<?php endif ?>
