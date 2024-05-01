<?php if ($record->is_two_factor_enabled): ?>
    <?= __(":name enabled two-factor authentication", ['name' => e($record->actor_user_name)]) ?>
<?php else: ?>
    <?= __(":name disabled two-factor authentication", ['name' => e($record->actor_user_name)]) ?>
<?php endif ?>
