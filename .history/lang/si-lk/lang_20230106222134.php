<?php

return [
    'plugin' => [
        'name' => 'පරිශීලක',
        'description' => 'ඉදිරිපස පරිශීලක කළමනාකරණය.',
        'tab' => 'පරිශීලකයන්',
        'access_users' => 'පරිශීලකයන් කළමනාකරණය කරන්න',
        'access_groups' => 'පරිශීලක කණ්ඩායම් කළමනාකරණය කරන්න',
        'access_settings' => 'පරිශීලක සැකසුම් කළමනාකරණය කරන්න',
        'impersonate_user' => 'පරිශීලකයන් ලෙස පෙනී සිටින්න'
    ],
    'users' => [
        'menu_label' => 'පරිශීලකයන්',
        'all_users' => 'සියලුම පරිශීලකයින්',
        'new_user' => 'නව පරිශීලක',
        'list_title' => 'පරිශීලකයන් කළමනාකරණය කරන්න',
        'trashed_hint_title' => 'පරිශීලකයා ඔවුන්ගේ ගිණුම අක්රිය කර ඇත',
        'trashed_hint_desc' => 'මෙම පරිශීලකයා ඔවුන්ගේ ගිණුම අක්‍රිය කර ඇති අතර තවදුරටත් අඩවියේ පෙනී සිටීමට අවශ්‍ය නැත. ඔවුන්ට නැවත පුරනය වීමෙන් ඕනෑම වේලාවක ඔවුන්ගේ ගිණුම ප්‍රතිසාධනය කළ හැක.',
        'banned_hint_title' => 'පරිශීලකයා තහනම් කර ඇත',
        'banned_hint_desc' => 'මෙම පරිශීලකයා පරිපාලකයෙකු විසින් තහනම් කර ඇති අතර ඔහුට පුරනය වීමට නොහැකි වනු ඇත.',
        'guest_hint_title' => 'මෙය ආගන්තුක පරිශීලකයෙකි',
        'guest_hint_desc' => 'මෙම පරිශීලකයා ගබඩා කර ඇත්තේ යොමු අරමුණු සඳහා පමණක් වන අතර පුරනය වීමට පෙර ලියාපදිංචි වීමට අවශ්‍ය වේ.',
        'activate_warning_title' => 'පරිශීලකයා සක්රිය කර නැත!',
        'activate_warning_desc' => 'මෙම පරිශීලකයා සක්‍රිය කර නොමැති අතර පුරනය වීමට නොහැකි විය හැක.',
        'activate_confirm' => 'ඔබට ඇත්තටම මෙම පරිශීලකයා සක්‍රිය කිරීමට අවශ්‍යද?',
        'activated_success' => 'පරිශීලකයා සක්රිය කර ඇත',
        'activate_manually' => 'මෙම පරිශීලකයා අතින් සක්රිය කරන්න',
        'convert_guest_confirm' => 'මෙම අමුත්තා පරිශීලකයෙකු බවට පරිවර්තනය කරන්නද?',
        'convert_guest_manually' => 'ලියාපදිංචි පරිශීලකයා බවට පරිවර්තනය කරන්න',
        'convert_guest_success' => 'පරිශීලක ලියාපදිංචි ගිණුමක් බවට පරිවර්තනය කර ඇත',
        'impersonate_user' => 'පරිශීලක ලෙස පෙනී සිටින්න',
        'impersonate_confirm' => 'මෙම පරිශීලකයා ලෙස පෙනී සිටින්නද? ලොග් අවුට් වීමෙන් ඔබට ඔබේ මුල් තත්වයට ආපසු යා හැක.',
        'impersonate_success' => 'ඔබ දැන් මෙම පරිශීලකයා ලෙස පෙනී සිටියි',
        'delete_confirm' => 'ඔබට ඇත්තටම මෙම පරිශීලකයා මකා දැමීමට අවශ්‍යද?',
        'unban_user' => 'මෙම පරිශීලක තහනම ඉවත් කරන්න',
        'unban_confirm' => 'ඔබට ඇත්තටම මෙම පරිශීලකයාගේ තහනම ඉවත් කිරීමට අවශ්‍යද?',
        'unbanned_success' => 'පරිශීලක තහනම ඉවත් කර ඇත',
        'return_to_list' => 'පරිශීලක ලැයිස්තුව වෙත ආපසු යන්න',
        'update_details' => 'යාවත්කාලීන විස්තර',
        'bulk_actions' => 'තොග ක්රියාවන්',
        'delete_selected' => 'තෝරාගත් මකන්න',
        'delete_selected_confirm' => 'තෝරාගත් පරිශීලකයින් මකන්නද?',
        'delete_selected_empty' => 'මකා දැමීමට තෝරාගත් පරිශීලකයන් නැත.',
        'delete_selected_success' => 'තෝරාගත් පරිශීලකයින් සාර්ථකව මකා දමන ලදී.',
        'activate_selected' => 'තෝරාගත් සක්රිය කරන්න',
        'activate_selected_confirm' => 'තෝරාගත් පරිශීලකයන් සක්රිය කරන්නද?',
        'activate_selected_empty' => 'සක්‍රිය කිරීමට තෝරාගත් පරිශීලකයන් නොමැත.',
        'activate_selected_success' => ' තෝරාගත් පරිශීලකයින් සාර්ථකව සක්‍රිය කර ඇත.',
        'deactivate_selected' => 'තෝරාගත් අක්රිය කරන්න',
        'deactivate_selected_confirm' => 'තෝරාගත් පරිශීලකයන් අක්‍රිය කරන්නද?',
        'deactivate_selected_empty' => 'අක්‍රිය කිරීමට තෝරාගත් පරිශීලකයන් නැත.',
        'deactivate_selected_success' => 'තෝරාගත් පරිශීලකයින් සාර්ථකව අක්‍රිය කර ඇත.',
        'restore_selected' => 'තෝරාගත් ප්‍රතිසාධනය කරන්න',
        'restore_selected_confirm' => 'තෝරාගත් පරිශීලකයන් ප්‍රතිසාධනය කරන්නද?',
        'restore_selected_empty' => 'ප්‍රතිසාධනය කිරීමට තෝරාගත් පරිශීලකයන් නැත.',
        'restore_selected_success' => 'තෝරාගත් පරිශීලකයින් සාර්ථකව ප්‍රතිසාධනය කරන ලදී.',
        'ban_selected' => 'තහනම් තෝරන ලදී',
        'ban_selected_confirm' => 'තෝරාගත් පරිශීලකයින් තහනම් කරන්නද?',
        'ban_selected_empty' => 'තහනම් කිරීමට තෝරාගත් පරිශීලකයන් නොමැත.',
        'ban_selected_success' => 'තෝරාගත් පරිශීලකයින් සාර්ථකව තහනම් කරන ලදී.',
        'unban_selected' => 'තහනම් ඉවත් කිරීම තෝරා ඇත',
        'unban_selected_confirm' => 'තෝරාගත් පරිශීලකයින් තහනම් නොකරන්නද?',
        'unban_selected_empty' => 'තහනම් කිරීම ඉවත් කිරීමට තෝරාගත් පරිශීලකයන් නොමැත.',
        'unban_selected_success' => 'තෝරාගත් පරිශීලකයින් සාර්ථකව තහනම් කිරීම ඉවත් කරන ලදී.',
        'unsuspend' => 'අත්හිටුවීම ඉවත් කරන්න',
        'unsuspend_success' => 'පරිශීලකයාගේ අත්හිටුවීම ඉවත් කර ඇත.',
        'unsuspend_confirm' => 'මෙම පරිශීලකයාගේ අත්හිටුවීම ඉවත් කරන්නද?'
    ],
    'settings' => [
        'users' => 'පරිශීලකයන්',
        'menu_label' => 'පරිශීලක සැකසුම්',
        'menu_description' => 'පරිශීලක සත්‍යාපනය, ලියාපදිංචිය සහ සක්‍රිය කිරීමේ සැකසුම් කළමනාකරණය කරන්න.',
        'activation_tab' => 'සක්රිය කිරීම',
        'signin_tab' => 'පුරන්න',
        'registration_tab' => 'ලියාපදිංචි කිරීම',
        'profile_tab' => 'පැතිකඩ',
        'notifications_tab' => 'දැනුම්දීම්',
        'allow_registration' => 'පරිශීලක ලියාපදිංචියට ඉඩ දෙන්න',
        'allow_registration_comment' => 'මෙය ආබාධිත නම්, පරිශීලකයින් නිර්මාණය කළ හැක්කේ පරිපාලකයින්ට පමණි.',
        'activate_mode' => 'සක්රිය කිරීමේ මාදිලිය',
        'activate_mode_comment' => 'පරිශීලක ගිණුමක් සක්රිය කළ යුතු ආකාරය තෝරන්න.',
        'activate_mode_auto' => 'ස්වයංක්රීය',
        'activate_mode_auto_comment' => '
        ලියාපදිංචි වීමෙන් පසු ස්වයංක්රීයව සක්රිය කර ඇත.',
        'activate_mode_user' => 'User',
        'activate_mode_user_comment' => 'The user activates their own account using mail.',
        'activate_mode_admin' => 'Administrator',
        'activate_mode_admin_comment' => 'Only an Administrator can activate a user.',
        'require_activation' => 'Sign in requires activation',
        'require_activation_comment' => 'Users must have an activated account to sign in.',
        'use_throttle' => 'Throttle attempts',
        'use_throttle_comment' => 'Repeat failed sign in attempts will temporarily suspend the user.',
        'use_register_throttle' => 'Throttle registration',
        'use_register_throttle_comment' => 'Prevent multiple registrations from the same IP in short succession.',
        'block_persistence' => 'Prevent concurrent sessions',
        'block_persistence_comment' => 'When enabled users cannot sign in to multiple devices at the same time.',
        'login_attribute' => 'Login attribute',
        'login_attribute_comment' => 'Select what primary user detail should be used for signing in.',
        'remember_login' => 'Remember login mode',
        'remember_login_comment' => 'Select if the user session should be persistent.',
        'remember_always' => 'Always',
        'remember_never' => 'Never',
        'remember_ask' => 'Ask the user on login',
    ],
    'user' => [
        'label' => 'User',
        'id' => 'ID',
        'username' => 'Username',
        'name' => 'Name',
        'name_empty' => 'Anonymous',
        'surname' => 'Surname',
        'email' => 'Email',
        'created_at' => 'Registered',
        'last_seen' => 'Last seen',
        'is_guest' => 'Guest',
        'joined' => 'Joined',
        'is_online' => 'Online now',
        'is_offline' => 'Currently offline',
        'send_invite' => 'Send invitation by email',
        'send_invite_comment' => 'Sends a welcome message containing login and password information.',
        'create_password' => 'Create Password',
        'create_password_comment' => 'Enter a new password used for signing in.',
        'reset_password' => 'Reset Password',
        'reset_password_comment' => 'To reset this users password, enter a new password here.',
        'confirm_password' => 'Password Confirmation',
        'confirm_password_comment' => 'Enter the password again to confirm it.',
        'groups' => 'Groups',
        'empty_groups' => 'There are no user groups available.',
        'avatar' => 'Avatar',
        'details' => 'Details',
        'account' => 'Account',
        'block_mail' => 'Block all outgoing mail sent to this user.',
        'status_label' => 'Status',
        'status_guest' => 'Guest',
        'status_activated' => 'Activated',
        'status_registered' => 'Registered',
        'created_ip_address' => 'Created IP Address',
        'last_ip_address' => 'Last IP Address',
    ],
    'group' => [
        'label' => 'Group',
        'id' => 'ID',
        'name' => 'Name',
        'description_field' => 'Description',
        'code' => 'Code',
        'code_comment' => 'Enter a unique code used to identify this group.',
        'created_at' => 'Created',
        'users_count' => 'Users'
    ],
    'groups' => [
        'menu_label' => 'Groups',
        'all_groups' => 'User Groups',
        'new_group' => 'New Group',
        'delete_selected_confirm' => 'Do you really want to delete selected groups?',
        'list_title' => 'Manage Groups',
        'delete_confirm' => 'Do you really want to delete this group?',
        'delete_selected_success' => 'Successfully deleted the selected groups.',
        'delete_selected_empty' => 'There are no selected groups to delete.',
        'return_to_list' => 'Back to groups list',
        'return_to_users' => 'Back to users list',
        'create_title' => 'Create User Group',
        'update_title' => 'Edit User Group',
        'preview_title' => 'Preview User Group'
    ],
    'login' => [
        'attribute_email' => 'Email',
        'attribute_username' => 'Username'
    ],
    'account' => [
        'account' => 'Account',
        'account_desc' => 'User management form.',
        'banned' => 'Sorry, this user is currently not activated. Please contact us for further assistance.',
        'redirect_to' => 'Redirect to',
        'redirect_to_desc' => 'Page name to redirect to after update, sign in or registration.',
        'code_param' => 'Activation Code Param',
        'code_param_desc' => 'The page URL parameter used for the registration activation code',
        'force_secure' => 'Force secure protocol',
        'force_secure_desc' => 'Always redirect the URL with the HTTPS schema.',
        'invalid_user' => 'A user was not found with the given credentials.',
        'invalid_activation_code' => 'Invalid activation code supplied.',
        'invalid_deactivation_pass' => 'The password you entered was invalid.',
        'invalid_current_pass' => 'The current password you entered was invalid.',
        'success_activation' => 'Successfully activated your account.',
        'success_deactivation' => 'Successfully deactivated your account. Sorry to see you go!',
        'success_saved' => 'Settings successfully saved!',
        'login_first' => 'You must be logged in first!',
        'already_active' => 'Your account is already activated!',
        'activation_email_sent' => 'An activation email has been sent to your email address.',
        'activation_by_admin' => 'You have successfully registered. Your account is not yet active and must be approved by an administrator.',
        'registration_disabled' => 'Registrations are currently disabled.',
        'registration_throttled' => 'Registration is throttled. Please try again later.',
        'sign_in' => 'Sign in',
        'register' => 'Register',
        'full_name' => 'Full Name',
        'email' => 'Email',
        'password' => 'Password',
        'login' => 'Login',
        'new_password' => 'New Password',
        'new_password_confirm' => 'Confirm New Password',
        'update_requires_password' => 'Confirm password on update',
        'update_requires_password_comment' => 'Require the current password of the user when changing their profile.',
        'activation_page' => 'Activation Page',
        'activation_page_comment' => 'Select a page to use for activating the user account',
        'reset_page' => 'Reset Page',
        'reset_page_comment' => 'Select a page to use for resetting the account password',
    ],
    'reset_password' => [
        'reset_password' => 'Reset Password',
        'reset_password_desc' => 'Forgotten password form.',
        'code_param' => 'Reset Code Param',
        'code_param_desc' => 'The page URL parameter used for the reset code'
    ],
    'session' => [
        'session' => 'Session',
        'session_desc' => 'Adds the user session to a page and can restrict page access.',
        'security_title' => 'Allow only',
        'security_desc' => 'Who is allowed to access this page.',
        'all' => 'All',
        'users' => 'Users',
        'guests' => 'Guests',
        'allowed_groups_title' => 'Allow groups',
        'allowed_groups_description' => 'Choose allowed groups or none to allow all groups',
        'redirect_title' => 'Redirect to',
        'redirect_desc' => 'Page name to redirect if access is denied.',
        'logout' => 'You have been successfully logged out!',
        'stop_impersonate_success' => 'You are no longer impersonating a user.',
    ]
];
