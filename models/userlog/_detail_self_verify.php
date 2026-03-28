<?= __(":name verified their email address :user_email", [
    'name' => $record->actor_user_name_linked,
    'user_email' => e($record->user_email),
]) ?>
