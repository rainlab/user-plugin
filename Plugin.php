<?php namespace Plugins\RainLab\User;

use Backend;
use Modules\System\Classes\PluginBase;
use October\Rain\Support\FacadeLoader;

class Plugin extends PluginBase
{

    public function pluginDetails()
    {
        return [
            'name' => 'User',
            'description' => 'Front-end user management.',
            'author' => 'Alexey Bobkov, Samuel Georges',
            'icon' => 'icon-user'
        ];
    }

    public function register()
    {
        $facade = FacadeLoader::instance();
        $facade->facade('Auth', 'Plugins\RainLab\User\Facades\Auth');
    }

    public function registerComponents()
    {
        return [
            'Plugins\RainLab\User\Components\Register' => 'userRegister',
            'Plugins\RainLab\User\Components\Signin' => 'userSignin',
        ];
    }

    public function registerNavigation()
    {
        return [
            'user' => [
                'label' => 'Users',
                'url' => Backend::url('october/user/users'),
                'icon' => 'icon-user',
                'permissions' => ['user:*'],
                'order' => 500,
                'sideMenu' => [
                    'users' => [
                        'label' => 'All Users',
                        'icon' => 'icon-user',
                        'url' => Backend::url('october/user/users'),
                        'permissions' => ['user:access_users'],
                    ],
                ]
            ]
        ];
    }

    public function registerSettings()
    {
        return [
            'location' => [
                'label' => 'Locations',
                'description' => 'Manage available user countries and states.',
                'category' => 'Users',
                'icon' => 'icon-globe',
                'url' => 'october/user/locations',
                'sort' => 100
            ]
        ];
    }

}