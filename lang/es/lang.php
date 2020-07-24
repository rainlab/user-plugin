<?php
return [
    "account" => [
        "account" => "Perfil",
        "account_desc" => "Formulario para gestionar datos del usuario.",
        "activation_by_admin" => "",
        "activation_email_sent" => "Un e-mail de confirmación ha sido enviado a su dirección de correo electrónico.",
        "already_active" => "¡Su perfil ya estaba activado!",
        "banned" => "Los sentimos, este usuario no se encuentra activado actualmente. Por favor contáctanos para mayor asistencia.",
        "code_param" => "Parámetro para el código de activación",
        "code_param_desc" => "El parámetro de URL en la página usuado para el código de activación del registro.",
        "email" => "E-mail",
        "force_secure" => "Forzar protocolo seguro",
        "force_secure_desc" => "Siempre redirigir la URL utilizando HTTPS.",
        "full_name" => "Nombre completo",
        "invalid_activation_code" => "El código de activación que proporcionó es inválido.",
        "invalid_current_pass" => "",
        "invalid_deactivation_pass" => "La contraseña que ingresó es inválida.",
        "invalid_user" => "No se encontro un usuario con las credenciales proporcionadas.",
        "login" => "Iniciar sesión",
        "login_first" => "¡Debe iniciar sesión primero!",
        "new_password" => "Nueva contraseña",
        "new_password_confirm" => "Confirme su nueva contraseña",
        "password" => "Contraseña",
        "redirect_to" => "redirigir a",
        "redirect_to_desc" => "Página hacia la cual redirigir despues de una actualización, inicio de sesión o registro.",
        "register" => "Registro",
        "registration_disabled" => "El registro de usuarios está temporalmente deshabilitado.",
        "registration_throttled" => "",
        "sign_in" => "Iniciar sesión",
        "success_activation" => "Su perfil fue activado exitosamente.",
        "success_deactivation" => "Su perfil fue desactivado exitosamente. ¡Nos apena mucho su partida!",
        "success_saved" => "¡Preferencias guardadas exitosamente!",
        "update_requires_password" => "",
        "update_requires_password_comment" => ""
    ],
    "country" => ["code" => "", "code_comment" => "", "enabled" => "", "label" => "", "name" => ""],
    "group" => [
        "code" => "Código",
        "code_comment" => "Ingrese un código único para identificar este grupo.",
        "created_at" => "Creado",
        "description_field" => "Descripción",
        "id" => "ID",
        "is_new_user_default_field" => "",
        "label" => "Grupo",
        "name" => "Nombre",
        "users_count" => "Usuarios"
    ],
    "groups" => [
        "all_groups" => "Grupos de usuarios",
        "create_title" => "Añadir grupo",
        "delete_confirm" => "¿Realmente desea eliminar este grupo?",
        "delete_selected_confirm" => "¿Realmente quiere eliminar todos los grupos seleccionados?",
        "delete_selected_empty" => "No hay grupos seleccionados para eliminar.",
        "delete_selected_success" => "Los grupos seleccionados fueron eliminados exitosamente.",
        "list_title" => "Administrar grupos",
        "menu_label" => "Grupos",
        "new_group" => "Nuevo grupo",
        "preview_title" => "Vista previa de grupo",
        "return_to_list" => "Volver a la lista de grupos",
        "return_to_users" => "Volver a la lista de usuarios",
        "update_title" => "Editar grupo"
    ],
    "location" => [
        "create_title" => "",
        "label" => "",
        "new" => "",
        "preview_title" => "",
        "update_title" => ""
    ],
    "locations" => [
        "delete_confirm" => "",
        "disable_confirm" => "",
        "disable_success" => "",
        "enable_or_disable" => "",
        "enable_or_disable_title" => "",
        "enable_success" => "",
        "enabled_help" => "",
        "enabled_label" => "",
        "hide_disabled" => "",
        "list_title" => "",
        "menu_description" => "",
        "menu_label" => "",
        "return_to_list" => "",
        "selected_amount" => ""
    ],
    "login" => ["attribute_email" => "Email", "attribute_username" => "Nombre de usuario"],
    "plugin" => [
        "access_groups" => "Administrar grupos",
        "access_settings" => "Preferencias de usuario",
        "access_users" => "Administrar usuarios",
        "description" => "Administración de usuarios en la interfaz del sistema.",
        "impersonate_user" => "Personificar Usuario",
        "name" => "Usuarios",
        "tab" => "Usuarios"
    ],
    "reset_password" => [
        "code_param" => "Parámetro para el código de restablecimiento",
        "code_param_desc" => "El parámetro de URL de la página usado para el código de restablecimiento.e",
        "reset_password" => "Restablecer contraseña",
        "reset_password_desc" => "Formulario de contraseña olvidada."
    ],
    "session" => [
        "all" => "Todos",
        "allowed_groups_description" => "Selecciona los grupos permitidos o ninguno para permitir todos los grupos",
        "allowed_groups_title" => "Permitir grupos",
        "guests" => "Invitados",
        "logout" => "¡Ha terminado exitosamente su sesión!",
        "redirect_desc" => "Página a la cual redirigir si el acceso es denegado.",
        "redirect_title" => "redirigir a",
        "security_desc" => "Quién es permitido acceder a esta página.",
        "security_title" => "Permitir solamente",
        "session" => "Sesión",
        "session_desc" => "Agrega la sesión del usuario a una página y puede restringir el acceso a dicha página.",
        "stop_impersonate_success" => "Has dejado de personificar al usuario.",
        "users" => "Usuarios"
    ],
    "settings" => [
        "activate_mode" => "Modo de activación",
        "activate_mode_admin" => "Administrador",
        "activate_mode_admin_comment" => "Sólo un administrador puede activar un usuario.",
        "activate_mode_auto" => "Automática",
        "activate_mode_auto_comment" => "Activada automáticamente al registrarse.",
        "activate_mode_comment" => "Seleccione como debería ser activado un perfil de usuario.",
        "activate_mode_user" => "Usuario",
        "activate_mode_user_comment" => "El usuario activa su perfil usando su e-mail.",
        "activation_tab" => "Activación",
        "allow_registration" => "Permitir registro de usuarios",
        "allow_registration_comment" => "Si está deshabilitado los usuarios sólo pueden ser creados por administradores.",
        "block_persistence" => "Prevenir sesiones concurrentes",
        "block_persistence_comment" => "Prevenir que los usuarios habilitados puedan iniciar sesión desde múltiples dispositivos.",
        "default_country" => "",
        "default_country_comment" => "",
        "default_state" => "",
        "default_state_comment" => "",
        "hint_templates" => "",
        "location_tab" => "",
        "login_attribute" => "Atributo de inicio de sesión",
        "login_attribute_comment" => "Seleccione que dato de usuario debería ser utilizado para el inicio de sesión.",
        "menu_description" => "Administra las preferencias de los usuarios.",
        "menu_label" => "Preferencias de usuario",
        "min_password_length" => "Largo mínimo de la contraseña",
        "min_password_length_comment" => "La cantidad mínima de caracteres requerida para contraseñas.",
        "no_mail_template" => "",
        "notifications_tab" => "Notificaciones",
        "profile_tab" => "",
        "registration_tab" => "Registro",
        "remember_always" => "Siempre",
        "remember_ask" => "Preguntar al usuario al iniciar sesión",
        "remember_login" => "Modo recordar usuario",
        "remember_login_comment" => "Selecciona si la sesión del usuario debe ser persistente.",
        "remember_never" => "Nunca",
        "require_activation" => "Inicio de sesión requiere activación.",
        "require_activation_comment" => "Usuarios deben tener una perfil activado para poder iniciar sesión.",
        "signin_tab" => "Iniciar sesión",
        "use_register_throttle" => "",
        "use_register_throttle_comment" => "",
        "use_throttle" => "Límite de intentos",
        "use_throttle_comment" => "Intentos de inicios de sesión seguidos que provocaran la suspensión temporal del perfil de usuario.",
        "users" => "Usuarios",
        "welcome_template" => "",
        "welcome_template_comment" => ""
    ],
    "state" => [
        "code" => "",
        "code_comment" => "",
        "label" => "",
        "name" => "",
        "name_comment" => ""
    ],
    "user" => [
        "account" => "Perfil",
        "avatar" => "Avatar",
        "block_mail" => "Bloquear todos los envios de e-mail para este usuario.",
        "confirm_password" => "Confirmación de contraseña",
        "confirm_password_comment" => "Ingrese la contraseña nuevamente para confirmarla.",
        "create_password" => "Crear contraseña",
        "create_password_comment" => "Ingrese nueva contraseña para iniciar sesión",
        "created_at" => "Registrado",
        "created_ip_address" => "",
        "details" => "Detalles",
        "email" => "E-mail",
        "empty_groups" => "No hay grupos de usuarios disponibles.",
        "groups" => "Grupos",
        "id" => "ID",
        "is_guest" => "Invitado",
        "is_offline" => "Actualmente desconectado",
        "is_online" => "En línea ahora",
        "joined" => "Incorporado",
        "label" => "Usuario",
        "last_ip_address" => "",
        "last_seen" => "Última vez visto",
        "name" => "Nombres",
        "name_empty" => "Anónimo",
        "reset_password" => "Restablecer contraseña",
        "reset_password_comment" => "Para restablecer la contraseña del usuario, ingrese una nueva contraseña aquí.",
        "send_invite" => "Enviar invitación por email",
        "send_invite_comment" => "Envía un mensaje de bienvenida con información de inicio de sesión y contraseña.",
        "status_activated" => "Activado",
        "status_guest" => "Invitado",
        "status_registered" => "Registrado",
        "surname" => "Apellidos",
        "user" => "",
        "username" => "Nombre de usuario"
    ],
    "users" => [
        "activate_confirm" => "¿Realmente desea activar este usuario?",
        "activate_manually" => "Activar usuario manualmente",
        "activate_selected" => "Activar usuarios seleccionados",
        "activate_selected_confirm" => "¿Activar los usuarios seleccionados?",
        "activate_selected_empty" => "No hay usuarios seleccionados para activar.",
        "activate_selected_success" => "Los usuarios seleccionados han sido activados exitosamente.",
        "activate_warning_desc" => "Este usuario todavía no ha sido activado y no será capaz de iniciar sesión.",
        "activate_warning_title" => "¡El usuario no se pudo activar!",
        "activated_success" => "¡El usuario ha sido activado exitosamente!",
        "activating" => "",
        "active_manually" => "",
        "all_users" => "Todos los usuarios",
        "ban_selected" => "Bloquear usuarios seleccionados",
        "ban_selected_confirm" => "¿Bloquear los usuarios seleccionados?",
        "ban_selected_empty" => "No hay usuarios seleccionados a bloquear.",
        "ban_selected_success" => "Se bloquearon exitosamente los usuarios seleccionados.",
        "banned_hint_desc" => "Este usuario ha sido bloqueado por un administrador y no será capaz de iniciar sesión.",
        "banned_hint_title" => "El Usuario ha sido bloqueado",
        "bulk_actions" => "Acciones en masa",
        "convert_guest_confirm" => "¿Convertir este invitado en usuario?",
        "convert_guest_manually" => "Convertir a usuario registrado",
        "convert_guest_success" => "El usuario ha sido convertido a una cuenta registrada",
        "deactivate_selected" => "Desactivar usuarios seleccionados",
        "deactivate_selected_confirm" => "¿Desactivar los usuarios seleccionados?",
        "deactivate_selected_empty" => "No hay usuarios seleccionados a desactivar.",
        "deactivate_selected_success" => "Se desactivaron exitosamente los usuarios seleccionados.",
        "delete_confirm" => "¿Realmente desea eliminar este usuario?",
        "delete_selected" => "Eliminar usuarios seleccionados",
        "delete_selected_confirm" => "¿Eliminar los usuarios seleccionados?",
        "delete_selected_empty" => "No hay usuarios seleccionados a eliminar.",
        "delete_selected_success" => "Se eliminaron exitosamente los usuarios seleccionados.",
        "guest_hint_desc" => "El usuario es almacenado sólo para efectos de referencia y necesita registrarse antes de iniciar sesión.",
        "guest_hint_title" => "Este usuario es un invitado",
        "impersonate_confirm" => "¿Estás seguro que quieres iniciar sesión como este usuario? Puedes volver a tu estado original cerrando la sesión.",
        "impersonate_success" => "Has iniciado sesión como este usuario correctamente.",
        "impersonate_user" => "Iniciar sesión como este usuario",
        "list_title" => "Administrar usuarios",
        "menu_label" => "Usuarios",
        "new_user" => "Nuevo usuario",
        "restore_selected" => "Restablecer los usuarios seleccionados",
        "restore_selected_confirm" => "¿Restablecer los usuarios seleccionados?",
        "restore_selected_empty" => "No hay usuarios seleccionados a restablecer.",
        "restore_selected_success" => "Se restablecieron exitosamente los usuarios seleccionados.",
        "return_to_list" => "Volver a la lista de usuarios",
        "trashed_hint_desc" => "Este usuario tiene desactivado su perfil y  no quiere aparecer en el sitio. Los usuarios pueden reactivar su perfil en cualquier momento iniciando sesión nuevamente.",
        "trashed_hint_title" => "El usuario tiene su perfil desactivado",
        "unban_confirm" => "¿Realmente desea desbloquear este usuario?",
        "unban_selected" => "Desbloquear usuarios seleccionados",
        "unban_selected_confirm" => "¿Desbloquear los usuarios seleccionados?",
        "unban_selected_empty" => "No hay usuarios seleccionados a desbloquear.",
        "unban_selected_success" => "Se desbloquearon exitosamente los usuarios seleccionados.",
        "unban_user" => "Desbloquear este usuario",
        "unbanned_success" => "El usuario ha sido desbloqueado",
        "unsuspend" => "",
        "unsuspend_confirm" => "",
        "unsuspend_success" => "",
        "update_details" => "Actualizar detalles"
    ]
];
