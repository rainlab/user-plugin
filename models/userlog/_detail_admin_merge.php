<?= __(":name merged :email into :user", [
    'name' => $record->actor_admin_name_linked,
    'email' => e($record->merged_user_email),
    'user' => $record->actor_user_name_linked,
]) ?>