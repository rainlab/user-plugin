<?php namespace RainLab\User;

use App;
use Event;
use Backend;
use System\Classes\PluginBase;
use Illuminate\Foundation\AliasLoader;
use RainLab\User\Models\MailBlocker;

class Plugin extends PluginBase
{

    public function pluginDetails()
    {
        return [
            'name'        => 'User',
            'description' => 'Front-end user management.',
            'author'      => 'Alexey Bobkov, Samuel Georges',
            'icon'        => 'icon-user'
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
        Event::listen('mailer.beforeSend', function($mailer, $view, $message){
            return MailBlocker::filterMessage($view, $message);
        });
    }

    public function registerComponents()
    {
        return [
            'RainLab\User\Components\Session'       => 'session',
            'RainLab\User\Components\Account'       => 'account',
            'RainLab\User\Components\ResetPassword' => 'resetPassword',
        ];
    }

    public function registerNavigation()
    {
        return [
            'user' => [
                'label'       => 'Users',
                'url'         => Backend::url('rainlab/user/users'),
                'icon'        => 'icon-user',
                'permissions' => ['users.*'],
                'order'       => 500,

                'sideMenu' => [
                    'users' => [
                        'label'       => 'All Users',
                        'icon'        => 'icon-user',
                        'url'         => Backend::url('rainlab/user/users'),
                        'permissions' => ['users.access_users'],
                    ],
                ]

            ]
        ];
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'User Settings',
                'description' => 'Manage user based settings.',
                'category'    => 'Users',
                'icon'        => 'icon-cog',
                'class'       => 'RainLab\User\Models\Settings',
                'sort'        => 100
            ],
            'location' => [
                'label'       => 'Locations',
                'description' => 'Manage available user countries and states.',
                'category'    => 'Users',
                'icon'        => 'icon-globe',
                'url'         => Backend::url('rainlab/user/locations'),
                'sort'        => 100
            ]
        ];
    }

    public function registerMailTemplates()
    {
        return [
            'rainlab.user::mail.activate' => 'Activation email sent to new users.',
            'rainlab.user::mail.restore' => 'Password reset instructions for front-end users.',
        ];
    }

    /**
     * Register new Twig variables
     * @return array
     */
    public function registerMarkupTags()
    {
        return [
            'functions' => [
                'form_select_country' => ['RainLab\User\Models\Country', 'formSelect'],
                'form_select_state' => ['RainLab\User\Models\State', 'formSelect'],
            ]
        ];
    }

}