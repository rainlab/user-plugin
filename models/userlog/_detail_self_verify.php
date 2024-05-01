<?= __(":name verified their email address :user_email", [
    'name' => e($record->actor_user_name),
    'user_email' => e($record->user_email),
]) ?>
