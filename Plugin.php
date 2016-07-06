<?php namespace RainLab\User;

use App;
use Event;
use Backend;
use System\Classes\PluginBase;
use System\Classes\SettingsManager;
use Illuminate\Foundation\AliasLoader;
use RainLab\User\Models\MailBlocker;

class Plugin extends PluginBase
{
    /**
     * @var boolean Determine if this plugin should have elevated privileges.
     */
    public $elevated = true;

    public function pluginDetails()
    {
        return [
            'name'        => 'rainlab.user::lang.plugin.name',
            'description' => 'rainlab.user::lang.plugin.description',
            'author'      => 'Alexey Bobkov, Samuel Georges',
            'icon'        => 'icon-user',
            'homepage'    => 'https://github.com/rainlab/user-plugin'
        ];
    }

    public function register()
    {
        $alias = AliasLoader::getInstance();
        $alias->alias('Auth', 'RainLab\User\Facades\Auth');

        App::singleton('user.auth', function() {
            return \RainLab\User\Classes\AuthManager::instance();
        });

        /*
         * Apply user-based mail blocking
         */
        Event::listen('mailer.prepareSend', function($mailer, $view, $message){
            return MailBlocker::filterMessage($view, $message);
        });
    }

    public function registerComponents()
    {
        return [
            'RainLab\User\Components\Session'           => 'session',
            'RainLab\User\Components\Account'           => 'account',
            'RainLab\User\Components\ResetPassword'     => 'resetPassword',
            'RainLab\User\Components\Login'             => 'login',
            'RainLab\User\Components\Signup'          => 'signup',
        ];
    }

    public function registerPermissions()
    {
        return [
            'rainlab.users.access_users' => ['tab' => 'rainlab.user::lang.plugin.tab', 'label' => 'rainlab.user::lang.plugin.access_users'],
            'rainlab.users.access_groups' => ['tab' => 'rainlab.user::lang.plugin.tab', 'label' => 'rainlab.user::lang.plugin.access_groups'],
            'rainlab.users.access_settings' => ['tab' => 'rainlab.user::lang.plugin.tab', 'label' => 'rainlab.user::lang.plugin.access_settings']
        ];
    }

    public function registerNavigation()
    {
        return [
            'user' => [
                'label'       => 'rainlab.user::lang.users.menu_label',
                'url'         => Backend::url('rainlab/user/users'),
                'icon'        => 'icon-user',
                'iconSvg'     => 'plugins/rainlab/user/assets/images/user-icon.svg',
                'permissions' => ['rainlab.users.*'],
                'order'       => 500,
            ]
        ];
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'rainlab.user::lang.settings.menu_label',
                'description' => 'rainlab.user::lang.settings.menu_description',
                'category'    => SettingsManager::CATEGORY_USERS,
                'icon'        => 'icon-cog',
                'class'       => 'RainLab\User\Models\Settings',
                'order'       => 500,
                'permissions' => ['rainlab.users.access_settings'],
            ]
        ];
    }

    public function registerMailTemplates()
    {
        return [
            'rainlab.user::mail.activate'   => 'rainlab.user::lang.mail_templates.activation',
            'rainlab.user::mail.welcome'    => 'rainlab.user::lang.mail_templates.welcome',
            'rainlab.user::mail.restore'    => 'rainlab.user::lang.mail_templates.restore',
            'rainlab.user::mail.new_user'   => 'rainlab.user::lang.mail_templates.new_user',
            'rainlab.user::mail.reactivate' => 'rainlab.user::lang.mail_templates.reactivate',
        ];
    }
}
