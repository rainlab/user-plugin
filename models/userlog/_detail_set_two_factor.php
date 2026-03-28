<?php if ($record->is_two_factor_enabled): ?>
    <?= __(":name enabled two-factor authentication", ['name' => $record->actor_user_name_linked]) ?>
<?php else: ?>
    <?= __(":name disabled two-factor authentication", ['name' => $record->actor_user_name_linked]) ?>
<?php endif ?>
