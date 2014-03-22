<?php namespace RainLab\User;

use Backend;
use System\Classes\PluginBase;
use October\Rain\Support\FacadeLoader;

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
        $facade = FacadeLoader::instance();
        $facade->facade('Auth', 'RainLab\User\Facades\Auth');
    }

    public function registerComponents()
    {
        return [
            'RainLab\User\Components\User'     => 'user',
            'RainLab\User\Components\Security' => 'userSecurity',
            'RainLab\User\Components\Register' => 'userRegister',
            'RainLab\User\Components\SignIn'   => 'userSignIn',
            'RainLab\User\Components\Reset'    => 'userReset',
            'RainLab\User\Components\Update'   => 'userUpdate',
        ];
    }

    public function registerNavigation()
    {
        return [
            'user' => [
                'label'       => 'Users',
                'url'         => Backend::url('rainlab/user/users'),
                'icon'        => 'icon-user',
                'permissions' => ['user:*'],
                'order'       => 500,

                'sideMenu' => [
                    'users' => [
                        'label'       => 'All Users',
                        'icon'        => 'icon-user',
                        'url'         => Backend::url('rainlab/user/users'),
                        'permissions' => ['user:access_users'],
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

}