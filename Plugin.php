<?php namespace Plugins\October\User;

use Backend;
use Modules\System\Classes\PluginBase;

class Plugin extends PluginBase
{

    public function pluginDetails()
    {
        return [
            'name' => 'User Plugin',
            'description' => 'Front-end user management.',
            'author' => 'Alexey Bobkov, Samuel Georges'
        ];
    }

    public function registerComponents()
    {
        return [
            'Plugins\October\User\Components\Register' => 'userRegister',
            'Plugins\October\User\Components\Signin' => 'userSignin',
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

}