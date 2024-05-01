<?php
    $replacements = [
        'old_value' => e($record->old_value),
        'new_value' => e($record->new_value)
    ];
?>
<?php if ($record->is_system): ?>
    <?= __(":name changed this user's email from :old_value to :new_value", $replacements + [
        'name' => e($record->actor_admin_name),
    ]) ?>
<?php else: ?>
    <?= __(":name changed their email from :old_value to :new_value", $replacements + [
        'name' => e($record->actor_user_name)
    ]) ?>
<?php endif ?>
