<?php

return [
    'plugin' => [
        'name' => 'Utilizador',
        'description' => 'Gestão de utilizadores de front-end.',
        'tab' => 'Utilizador',
        'access_users' => 'Gerir utilizadores',
        'access_groups' => 'Gerir grupos de utilizadores',
        'access_settings' => 'Gerir definições de utilizador'
    ],
    'users' => [
        'menu_label' => 'Utilizadores',
        'all_users' => 'Todos os utilizadores',
        'new_user' => 'Novo Utilizador',
        'list_title' => 'Gerir utilizadores',
        'trashed_hint_title' => 'O utilizador desactivou esta conta.',
        'trashed_hint_desc' => 'O utilizador desactivou esta conta e não pretende aceder ao site. O mesmo pode restaurar a conta em qualquer altura fazendo novamente a entrada.',
        'banned_hint_title' => 'O utilizador foi banido.',
        'banned_hint_desc' => 'Este utilizador foi banido por um administrador e não conseguirá fazer a entrada.',
        'guest_hint_title' => 'Utilizador convidado.',
        'guest_hint_desc' => 'Utilizador guardado para fins de referência e apenas precisa fazer registo antes de poder entrar.',
        'activate_warning_title' => 'Utilizador não activado!',
        'activate_warning_desc' => 'Este utilizador não foi activado e estará impossibilitado de entrar.',
        'activate_confirm' => 'Deseja ativar este utilizador?',
        'activated_success' => 'O Utilizador foi ativado com sucesso!',
        'activate_manually' => 'Activar este utilizador manualmente',
        'convert_guest_confirm' => 'Converter este convidado para utilizador?',
        'convert_guest_manually' => 'Converter para utilizador registado.',
        'convert_guest_success' => 'O utilizador foi convertido para uma conta registada',
        'delete_confirm' => 'Deseja eliminar este utilizador?',
        'unban_user' => 'Remover banimento a este utilizador',
        'unban_confirm' => 'Pretende remover banimento a este utilizador?',
        'unbanned_success' => 'Foi removido banimento deste utilizador',
        'return_to_list' => 'Regressar à lista de Utilizadores',
        'update_details' => 'Actualizar detalhes',
        'bulk_actions' => 'Acções em bloco',
        'delete_selected' => 'Eliminar seleccionados',
        'delete_selected_confirm' => 'Eliminar os utilizadores selecionados?',
        'delete_selected_empty' => 'Não há utilizadores selecionados para eliminar.',
        'delete_selected_success' => 'Utilizadores selecionados eliminados com sucesso.',
        'deactivate_selected' => 'Inactivar utilizadores seleccionados',
        'deactivate_selected_confirm' => 'Inactivar utilizadores seleccionados?',
        'deactivate_selected_empty' => 'Não existem utilizadores seleccionados para inactivar.',
        'deactivate_selected_success' => 'Utilizadores seleccionados inactivados com sucesso.',
        'restore_selected' => 'Restaurar utilizadores seleccionados',
        'restore_selected_confirm' => 'Restaurar utilizadores seleccionados?',
        'restore_selected_empty' => 'Não existem utilizadores seleccionados para restaurar.',
        'restore_selected_success' => 'Utilizadores seleccionados restaurados com sucesso.',
        'ban_selected' => 'Banir utilizadores seleccionados',
        'ban_selected_confirm' => 'Banir utilizadores seleccionados?',
        'ban_selected_empty' => 'Não existem utilizadores seleccionados para banir.',
        'ban_selected_success' => 'Utilizadores seleccionados banidos com sucesso.',
        'unban_selected' => 'Remover banimento a utilizadores seleccionados',
        'unban_selected_confirm' => 'Remover banimento a utilizadores seleccionados?',
        'unban_selected_empty' => 'Não existem utilizadores seleccionados para remover banimento.',
        'unban_selected_success' => 'Removido banimento com sucesso aos utilizadores seleccionados.',
    ],
    'settings' => [
        'users' => 'Utilizadores',
        'menu_label' => 'Configurações de Utilizador',
        'menu_description' => 'Gerir configurações relacionadas a Utilizadores.',
        'activation_tab' => 'Activação',
        'signin_tab' => 'Entrar',
        'registration_tab' => 'Registo',
        'notifications_tab' => 'Notificatções',
        'allow_registration' => 'Permitir registo de utilizador',
        'allow_registration_comment' => 'Se inactivo os utilizadores só poderão ser criados por administradores.',
        'activate_mode' => 'Modo de activação',
        'activate_mode_comment' => 'Selecione como uma conta de utilizador deve ser activada.',
        'activate_mode_auto' => 'Automático',
        'activate_mode_auto_comment' => 'Activada automaticamente mediante o registo.',
        'activate_mode_user' => 'Utilizador',
        'activate_mode_user_comment' => 'O utilizador activa a própria conta usando o e-mail.',
        'activate_mode_admin' => 'Administrador',
        'activate_mode_admin_comment' => 'Apenas um Administrador pode activar um utilizador.',
        'welcome_template' => 'Modelo de Boas Vindas',
        'welcome_template_comment' => 'Modelo de e-mail a ser enviado ao utilizador quanto ele é activado.',
        'require_activation' => 'Permissão para entrar requer ativação',
        'require_activation_comment' => 'Utilizadores precisam ter uma conta activada para entrar.',
        'use_throttle' => 'Tentativas limitadas',
        'use_throttle_comment' => 'Tentativas repetidas de entrada mal-sucedidas suspenderão temporariamente o utilizador.',
        'login_attribute' => 'Atributo para entrar',
        'login_attribute_comment' => 'Selecione qual atributo do utilizador deve ser usado para entrar.',
        'no_mail_template' => 'Não enviar uma notificação',
        'hint_templates' => 'Pode personalizar modelos de e-mail selecionando E-mail > Modelos de E-mail no menu de configurações.'
    ],
    'user' => [
        'label' => 'Utilizador',
        'id' => 'ID',
        'username' => 'Nome de utilizador',
        'name' => 'Nome',
        'name_empty' => 'Anonymous',
        'surname' => 'Último nome',
        'email' => 'E-mail',
        'created_at' => 'Registado em',
        'last_seen' => 'Visto po último',
        'is_guest' => 'Convidado',
        'joined' => 'Registou-se a',
        'is_online' => 'Online agora',
        'is_offline' => 'Desligado',
        'send_invite' => 'E-mail de convite enviado',
        'send_invite_comment' => 'Emvia uma mensagem de Boas Vindas contendo informações de acesso.',
        'create_password' => 'Criar password',
        'create_password_comment' => 'Informe uma password para entrada do utilizador.',
        'reset_password' => 'Redefinir password',
        'reset_password_comment' => 'Para redefinir a password deste utilizador, informe uma nova password aqui.',
        'confirm_password' => 'Confirmação de password',
        'confirm_password_comment' => 'Informe a password novamente para confirmá-la.',
        'groups' => 'Grupos',
        'empty_groups' => 'Não há grupos disponíveis.',
        'avatar' => 'Avatar',
        'details' => 'Detalhes',
        'account' => 'Conta',
        'block_mail' => 'Bloquear todos os envios de e-mail para este Utilizador.',
        'status_guest' => 'Convidado',
        'status_activated' => 'Activado',
        'status_registered' => 'Registado',
    ],
    'group' => [
        'label' => 'Grupo',
        'id' => 'ID',
        'name' => 'Nome',
        'description_field' => 'Descrição',
        'code' => 'Código',
        'code_comment' => 'Introduza um código único para identificar este grupo.',
        'created_at' => 'Criado',
        'users_count' => 'Utilizadores'
    ],
    'groups' => [
        'menu_label' => 'Grupos',
        'all_groups' => 'Grupos de utilizador',
        'new_group' => 'Novo grupo',
        'delete_selected_confirm' => 'Pretende eliminar os grupos seleccionados?',
        'list_title' => 'Gerir grupos',
        'delete_confirm' => 'Pretende eliminar este grupo?',
        'delete_selected_success' => 'Grupos seleccionados eliminados com sucesso.',
        'delete_selected_empty' => 'Não existem grupos seleccionados para elimianr.',
        'return_to_list' => 'Regressar à lista de grupos',
        'return_to_users' => 'Regressar à lista de utilizadores',
        'create_title' => 'Criar Grupo de utilizadores',
        'update_title' => 'Editar grupo de utilizadores',
        'preview_title' => 'Previsão de grupo de utilizadores'
    ],
    'login' => [
        'attribute_email' => 'E-mail',
        'attribute_username' => 'Nome de utilizador'
    ],
    'account' => [
        'account' => 'Conta',
        'account_desc' => 'Formulário de gerenciamento de utilizador.',
        'redirect_to' => 'Redirecionar para',
        'redirect_to_desc' => 'Nome da página para a qual redirecionar após atualização, entrada ou registo.',
        'code_param' => 'Parâmetro de Código de Ativação',
        'code_param_desc' => 'O parâmetro de URL da página usado para o código de ativação de registo',
        'invalid_user' => 'Não foi encontrado um utilizador com as credenciais informadas.',
        'invalid_activation_code' => 'Código de ativação inserido é inválido',
        'invalid_deactivation_pass' => 'A password inserida é inválida.',
        'success_activation' => 'Conta foi ativada com sucesso.',
        'success_deactivation' => 'A conta foi inactivada com sucesso. Lamentamos vê-lo ir-se embora!',
        'success_saved' => 'Configurações guardadas com sucesso!',
        'login_first' => 'Precisa efectuar a entrada primeiro!',
        'already_active' => 'A sua conta já está ativada!',
        'activation_email_sent' => 'E-mail de activação foi enviado para o endereço de e-mail fornecido.',
        'registration_disabled' => 'Registrations are currently disabled.',
        'sign_in' => 'Entrar',
        'register' => 'Fazer registo',
        'full_name' => 'Nome Completo',
        'email' => 'E-mail',
        'password' => 'Password',
        'login' => 'Entrar',
        'new_password' => 'Nova Password',
        'new_password_confirm' => 'Confirmar nova password'
    ],
    'reset_password' => [
        'reset_password' => 'Redefinir password',
        'reset_password_desc' => 'Formulário de redefinição de password .',
        'code_param' => 'Parâmetro de código para redefinir password ',
        'code_param_desc' => 'O parâmetro de URL da página usado para o código'
    ],
    'session' => [
        'session' => 'Sessão',
        'session_desc' => 'Adiciona a sessão do utilizador a uma página e pode restringir o acesso à página.',
        'security_title' => 'Permitir apenas',
        'security_desc' => 'Quem tem permissão para aceder a esta página.',
        'all' => 'Todos',
        'users' => 'Utilizadores',
        'guests' => 'Visitantes',
        'redirect_title' => 'Redirecionar para',
        'redirect_desc' => 'Nome da página para qual redirecionar se o acesso for negado.',
        'logout' => 'A sessão foi terminada com sucesso!'
    ]
];
