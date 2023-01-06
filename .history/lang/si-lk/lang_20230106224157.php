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
        'activate_mode_auto_comment' => 'ලියාපදිංචි වීමෙන් පසු ස්වයංක්රීයව සක්රිය කර ඇත.',
        'activate_mode_user' => 'පරිශීලක',
        'activate_mode_user_comment' => 'පරිශීලකයා තැපෑල භාවිතයෙන් තමන්ගේම ගිණුම සක්රිය කරයි.',
        'activate_mode_admin' => 'පරිපාලක',
        'activate_mode_admin_comment' => 'පරිශීලකයෙකු සක්‍රිය කළ හැක්කේ පරිපාලකයෙකුට පමණි.',
        'require_activation' => 'පුරනය වීමට සක්‍රිය කිරීම අවශ්‍ය වේ',
        'require_activation_comment' => 'පුරනය වීමට පරිශීලකයින්ට සක්‍රිය කළ ගිණුමක් තිබිය යුතුය.',
        'use_throttle' => 'Throttle උත්සාහයන්',
        'use_throttle_comment' => 'නැවත නැවත අසාර්ථක පුරනය වීමේ උත්සාහයන් පරිශීලකයා තාවකාලිකව අත්හිටුවනු ඇත.',
        'use_register_throttle' => 'Throttle ලියාපදිංචිය',
        'use_register_throttle_comment' => 'කෙටි කාලීනව එකම IP වෙතින් බහු ලියාපදිංචි කිරීම් වලක්වන්න.',
        'block_persistence' => 'සමගාමී සැසි වැළැක්වීම',
        'block_persistence_comment' => 'සබල කළ විට පරිශීලකයින්ට එකවර උපාංග කිහිපයකට පුරනය විය නොහැක.',
        'login_attribute' => 'පිවිසුම් ගුණාංගය',
        'login_attribute_comment' => 'පුරනය වීම සඳහා භාවිතා කළ යුතු මූලික පරිශීලක විස්තර තෝරන්න.',
        'remember_login' => 'පිවිසුම් මාදිලිය මතක තබා ගන්න',
        'remember_login_comment' => 'පරිශීලක සැසිය ස්ථීර විය යුතුද යන්න තෝරන්න.',
        'remember_always' => 'සැමවිටම',
        'remember_never' => 'කවදාවත් නැහැ',
        'remember_ask' => 'පුරනය වීමේදී පරිශීලකයාගෙන් විමසන්න',
    ],
    'user' => [
        'label' => 'පරිශීලක',
        'id' => 'හැඳුනුම්පත',
        'username' => 'පරිශීලක නාමය',
        'name' => 'නම',
        'name_empty' => 'නිර්නාමික',
        'surname' => 'වාසගම',
        'email' => 'විද්යුත් තැපෑල',
        'created_at' => 'ලියාපදිංචි',
        'last_seen' => 'අන්තිමට දැක්කේ',
        'is_guest' => 'අමුත්තන්ගේ',
        'joined' => 'එකතු වුණා',
        'is_online' => 'දැන් ඔන්ලයින්',
        'is_offline' => 'දැනට නොබැඳි',
        'send_invite' => 'විද්‍යුත් තැපෑලෙන් ආරාධනාවක් යවන්න',
        'send_invite_comment' => 'පිවිසුම් සහ මුරපද තොරතුරු අඩංගු පිළිගැනීමේ පණිවිඩයක් යවයි.',
        'create_password' => 'මුරපදය සාදන්න',
        'create_password_comment' => 'පුරනය වීමට භාවිතා කරන නව මුරපදයක් ඇතුලත් කරන්න.',
        'reset_password' => 'මුරපදය නැවත සකසන්න',
        'reset_password_comment' => 'මෙම පරිශීලක මුරපදය නැවත සැකසීමට, මෙහි නව මුරපදයක් ඇතුළත් කරන්න.',
        'confirm_password' => 'මුරපදය තහවුරු කිරීම',
        'confirm_password_comment' => 'එය තහවුරු කිරීමට මුරපදය නැවත ඇතුල් කරන්න.',
        'groups' => 'කණ්ඩායම්',
        'empty_groups' => 'ලබා ගත හැකි පරිශීලක කණ්ඩායම් නොමැත.',
        'avatar' => 'පැතිකඩ පින්තූරය',
        'details' => 'විස්තර',
        'account' => 'ගිණුම',
        'block_mail' => 'මෙම පරිශීලකයාට යවන සියලුම පිටතට යන තැපැල් අවහිර කරන්න.',
        'status_label' => 'තත්ත්වය',
        'status_guest' => 'අමුත්තන්ගේ',
        'status_activated' => 'සක්රිය කර ඇත',
        'status_registered' => 'ලියාපදිංචි',
        'created_ip_address' => 'සාදන ලද IP ලිපිනය',
        'last_ip_address' => 'අවසාන IP ලිපිනය',
    ],
    'group' => [
        'label' => 'සමූහය',
        'id' => 'හැඳුනුම්පත',
        'name' => 'නම',
        'description_field' => 'විස්තර',
        'code' => 'කේතය',
        'code_comment' => 'මෙම කණ්ඩායම හඳුනා ගැනීමට භාවිතා කරන අද්විතීය කේතයක් ඇතුළත් කරන්න.',
        'created_at' => 'නිර්මාණය කළා',
        'users_count' => 'පරිශීලකයන්'
    ],
    'groups' => [
        'menu_label' => 'කණ්ඩායම්',
        'all_groups' => 'පරිශීලක කණ්ඩායම්',
        'new_group' => 'නව සමූහය',
        'delete_selected_confirm' => 'ඔබට ඇත්තටම තෝරාගත් කණ්ඩායම් මැකීමට අවශ්‍යද?',
        'list_title' => 'කණ්ඩායම් කළමනාකරණය කරන්න',
        'delete_confirm' => 'ඔබට ඇත්තටම මෙම සමූහය මැකීමට අවශ්‍යද?',
        'delete_selected_success' => 'තෝරාගත් කණ්ඩායම් සාර්ථකව මකා දමන ලදී.',
        'delete_selected_empty' => 'මකා දැමීමට තෝරාගත් කණ්ඩායම් නොමැත.',
        'return_to_list' => 'කණ්ඩායම් ලැයිස්තුව වෙත ආපසු යන්න',
        'return_to_users' => 'පරිශීලක ලැයිස්තුව වෙත ආපසු යන්න',
        'create_title' => 'පරිශීලක කණ්ඩායමක් සාදන්න',
        'update_title' => 'පරිශීලක කණ්ඩායම සංස්කරණය කරන්න',
        'preview_title' => 'පරිශීලක කණ්ඩායම පෙරදසුන් කරන්න'
    ],
    'login' => [
        'attribute_email' => 'විද්යුත් තැපෑල',
        'attribute_username' => 'පරිශීලක නාමය'
    ],
    'account' => [
        'account' => 'ගිණුම',
        'account_desc' => 'පරිශීලක කළමනාකරණ පෝරමය.',
        'banned' => 'සමාවන්න, මෙම පරිශීලකයා දැනට සක්‍රිය කර නැත. වැඩිදුර සහාය සඳහා කරුණාකර අප හා සම්බන්ධ වන්න.',
        'redirect_to' => 'වෙත හරවා යවන්න',
        'redirect_to_desc' => 'යාවත්කාලීන කිරීම, පුරනය වීම හෝ ලියාපදිංචි වීමෙන් පසු නැවත යොමු කිරීමට පිටුවේ නම.',
        'code_param' => 'සක්රිය කිරීමේ කේතය පරාමිතිය',
        'code_param_desc' => 'ලියාපදිංචි සක්‍රිය කිරීමේ කේතය සඳහා භාවිතා කරන පිටු URL පරාමිතිය',
        'force_secure' => 'ආරක්ෂිත ප්‍රොටෝකෝලය බල කරන්න',
        'force_secure_desc' => 'සෑම විටම URL HTTPS ක්‍රමයෙන් හරවා යවන්න.',
        'invalid_user' => 'ලබා දී ඇති අක්තපත්‍ර සමඟ පරිශීලකයෙකු හමු නොවීය.',
        'invalid_activation_code' => 'වලංගු නොවන සක්‍රීය කිරීමේ කේතයක් සපයා ඇත.',
        'invalid_deactivation_pass' => 'ඔබ ඇතුළු කළ මුරපදය වලංගු නොවේ.',
        'invalid_current_pass' => 'ඔබ ඇතුළත් කළ වත්මන් මුරපදය වලංගු නොවේ.',
        'success_activation' => 'ඔබගේ ගිණුම සාර්ථකව සක්‍රිය කර ඇත.',
        'success_deactivation' => 'ඔබගේ ගිණුම සාර්ථකව අක්‍රිය කර ඇත. ඔබ යනවා දැකීම ගැන කණගාටුයි!',
        'success_saved' => 'සැකසීම් සාර්ථකව සුරකින ලදී!',
        'login_first' => 'ඔබ මුලින්ම ලොග් විය යුතුය!',
        'already_active' => 'ඔබගේ ගිණුම දැනටමත් සක්‍රිය කර ඇත!',
        'activation_email_sent' => 'ඔබගේ ඊමේල් ලිපිනයට සක්‍රිය කිරීමේ විද්‍යුත් තැපෑලක් යවා ඇත.',
        'activation_by_admin' => 'ඔබ සාර්ථකව ලියාපදිංචි වී ඇත. ඔබගේ ගිණුම තවමත් සක්‍රිය නොවන අතර පරිපාලකයෙකු විසින් අනුමත කළ යුතුය.',
        'registration_disabled' => 'ලියාපදිංචි කිරීම් දැනට අබල කර ඇත.',
        'registration_throttled' => 'ලියාපදිංචිය අවහිර කර ඇත. කරුණාකර පසුව නැවත උත්සාහ කරන්න.',
        'sign_in' => 'පුරන්න',
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
