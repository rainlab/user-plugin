<?php

return [
    'plugin' => [
        'name' => 'Benutzer',
        'description' => 'Frontend-Benutzer Verwaltung.',
        'tab' => 'Benutzer',
        'access_users' => 'Benutzer verwalten'
    ],
    'location' => [
        'label' => 'Standort',
        'new' => 'Neuer Standort',
        'create_title' => 'Standort anlegen',
        'update_title' => 'Standort bearbeiten',
        'preview_title' => 'Standort ansehen'
    ],
    'locations' => [
        'menu_label' => 'Standorte',
        'menu_description' => 'Verfügbare Benutzer-Standorte und Provinzen verwalten.',
        'hide_disabled' => 'Deaktivierte verbergen',
        'enabled_label' => 'Aktiviert',
        'enabled_help' => 'Deaktivierte Standorte sind im Frontend nicht sichtbar.',
        'enable_or_disable_title' => "Standorte aktivieren oder deaktivieren",
        'enable_or_disable' => 'Aktivieren oder deaktivieren',
        'selected_amount' => 'Standorte ausgewählt: :amount',
        'enable_success' => 'Standorte erfolgreich aktiviert.',
        'disable_success' => 'Standorte erfolgreich deaktiviert.',
        'disable_confirm' => 'Sind Sie sicher?',
        'list_title' => 'Standorte verwalten',
        'delete_confirm' => 'Möchten Sie diesen Standort wirklich löschen?',
        'return_to_list' => 'Zurück zur Standortliste'
    ],
    'users' => [
        'menu_label' => 'Benutzer',
        'all_users' => 'Alle Benutzer',
        'new_user' => 'Neuer Benutzer',
        'list_title' => 'Benutzer verwalten',
        'activating' => 'Aktiviere...',
        'activate_warning_title' => 'Benutzer nicht aktiviert!',
        'activate_warning_desc' => 'Dieser Benutzer wurde noch nicht aktiviert und kann sich nicht einloggen.',
        'activate_confirm' => 'Möchten Sie diesen Benutzer wirklich aktivieren?',
        'active_manually' => 'Benutzer manuell aktivieren',
        'delete_confirm' => 'Möchten Sie diesen Benutzer wirklich löschen?',
        'activated_success' => 'Benutzer erfolgreich aktiviert!',
        'return_to_list' => 'Zurück zur Benutzerliste',
        'delete_selected_empty' => 'Keine Benutzer zum Löschen ausgewählt.',
        'delete_selected_confirm' => 'Ausgewählte Benutzer löschen?',
        'delete_selected_success' => 'Ausgewählte Benutzer erfolgreich gelöscht.'
    ],
    'settings' => [
        'users' => 'Benutzer',
        'menu_label' => 'Benutzer Einstellungen',
        'menu_description' => 'Benutzer Einstellungen verwalten.',
        'activation_tab' => 'Aktivierung',
        'location_tab' => 'Standort',
        'signin_tab' => 'Einloggen',
        'activate_mode' => 'Aktivierungsmodus',
        'activate_mode_comment' => 'Wählen Sie aus, wie ein Benutzer aktiviert werden soll.',
        'activate_mode_auto' => 'Automatisch',
        'activate_mode_auto_comment' => 'Automatische Aktivierung bei Registrierung.',
        'activate_mode_user' => 'Benutzer',
        'activate_mode_user_comment' => 'Der Benutzer aktiviert seinen eigenen Account per E-Mail.',
        'activate_mode_admin' => 'Administrator',
        'activate_mode_admin_comment' => 'Nur ein Administrator kann einen Benutzer aktivieren.',
        'welcome_template' => 'Willkommen Template',
        'welcome_template_comment' => 'E-Mail Template, dass bei erster Aktivierung an Benutzer verschickt wird.',
        'require_activation' => 'Login benötigt Aktivierung',
        'require_activation_comment' => 'Benutzer müssen zum Einloggen einen aktivierten Account besitzen.',
        'default_country' => 'Standort Voreinstellung',
        'default_country_comment' => 'Wenn ein Benutzer keinen Standort angibt, wird dieser Standort als Standard gewählt.',
        'default_state' => 'Provinz Voreinstellung',
        'default_state_comment' => 'Wenn ein Benutzer keinen Standort angibt, wird diese Provinz als Standard gewählt.',
        'use_throttle' => 'Loginversuche begrenzen',
        'use_throttle_comment' => 'Bei wiederholten Login-Fehlversuchen wird der Benutzer temporär gesperrt.',
        'login_attribute' => 'Login Attribut-Zuordnung',
        'login_attribute_comment' => 'Wählen Sie, welches Benutzerattribut zum Einloggen verwendet werden soll.',
        'no_mail_template' => 'Keine Benachrichtigung senden',
        'hint_templates' => 'Sie können E-Mail-Vorlagen im Settings-Menü Mail > Mail Templates bearbeiten.'
    ],
    'state' => [
        'label' => 'Provinz',
        'name' => 'Name',
        'name_comment' => 'Anzeigenamen für diese Provinz eingeben.',
        'code' => 'Code',
        'code_comment' => 'Eindeutigen Code für diese Provinz eingeben.'
    ],
    'country' => [
        'label' => 'Land',
        'name' => 'Name',
        'code' => 'Code',
        'code_comment' => 'Eindeutigen Code für dieses Land eingeben.',
        'enabled' => 'Aktiv'
    ],
    'user' => [
        'label' => 'Benutzer',
        'id' => 'ID',
        'username' => 'Benutzername',
        'name' => 'Name',
        'surname' => 'Nachname',
        'email' => 'E-Mail',
        'created_at' => 'Registriert',
        'phone' => 'Telefon',
        'company' => 'Firma',
        'city' => 'Ort',
        'zip' => 'PLZ',
        'street_addr' => 'Adresse',
        'country' => 'Land',
        'select_country' => '-- Land auswählen --',
        'state' => 'Provinz',
        'select_state' => '-- Provinz auswählen --',
        'reset_password' => 'Passwort setzen',
        'reset_password_comment' => 'Geben Sie hier ein Passwort ein, um das aktuelle Benutzerpasswort zu überschreiben.',
        'confirm_password' => 'Passwort bestätigen',
        'confirm_password_comment' => 'Passwort zur Bestätigung erneut eingeben.',
        'avatar' => 'Avatar',
        'details' => 'Details',
        'account' => 'Benutzerkonto'
    ],
    'login' => [
        'attribute_email' => 'E-Mail',
        'attribute_username' => 'Benutzername'
    ],
    'account' => [
        'account' => 'Benutzerkonto',
        'account_desc' => 'Benutzerkontoverwaltung.',
        'redirect_to' => 'Weiterleiten nach',
        'redirect_to_desc' => 'Seitenname zum Weiterleiten nach Update, Login oder Registrierung.',
        'code_param' => 'Aktivierungscode Parameter',
        'code_param_desc' => 'Dieser URL-Parameter wird als Registrierungs-Aktivierungscode verwendet',
        'invalid_activation_code' => 'Ungültiger Aktivierungscode übermittelt',
        'invalid_user' => 'Es wurde kein Benutzer mit diesen Zugangsdaten gefunden.',
        'success_activation' => 'Benutzerkonto erfolgreich aktiviert.',
        'success_saved' => 'Einstellungen erfolgreich gespeichert!',
        'login_first' => 'Sie müssen sich erst einloggen!',
        'alredy_active' => 'Ihr Benutzerkonto ist bereits aktiviert!',
        'activation_email_sent' => 'Eine Aktivierungs-E-Mail wurde an Ihre E-Mail-Adresse versendet.',
        'country' => 'Land',
        'state' => 'Provinz',
        'sign_in' => 'Login',
        'register' => 'Registrieren',
        'full_name' => 'Name',
        'email' => 'E-Mail',
        'password' => 'Passwort',
        'register' => 'Registrieren',
        'login' => 'Login',
        'address' => 'Adresse',
        'city_suburb' => 'Stadt',
        'postal_code' => 'PLZ',
        'new_password' => 'Neues Password',
        'new_password_confirm' => 'Neues Password bestätigen'
    ],
    'reset_password' => [
        'reset_password' => 'Passwort zurücksetzen',
        'reset_password_desc' => 'Formular zum Zurücksetzen des Passworts.',
        'code_param' => 'Reset Code Parameter',
        'code_param_desc' => 'URL-Parameter, der für den Reset-Code verwendet wird'
    ],
    'session' => [
        'session' => 'Session',
        'session_desc' => 'Fügt Benutzer-Session zu Seite hinzu und kann Zugriff einschränken.',
        'security_title' => 'Erlauben',
        'security_desc' => 'Wer hat Zugriff auf die Seite?',
        'all' => 'Jeder',
        'users' => 'Benutzer',
        'guests' => 'Gäste',
        'redirect_title' => 'Weiterleiten nach',
        'redirect_desc' => 'Seitenname zum Weiterleiten bei verweigertem Zugriff.',
        'logout' => 'Sie haben sich erfolgreich ausgeloggt!'
    ]
];
