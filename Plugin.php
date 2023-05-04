<?php namespace RainLab\User;

use App;
use Auth;
use Event;
use Backend;
use System\Classes\PluginBase;
use System\Classes\SettingsManager;
use Illuminate\Foundation\AliasLoader;
use RainLab\User\Classes\UserRedirector;
use RainLab\User\Models\MailBlocker;
use RainLab\Notify\Classes\Notifier;

/**
 * Plugin base class
 */
class Plugin extends PluginBase
{
    /**
     * pluginDetails
     */
    public function pluginDetails()
    {
        return [
            'name' => 'rainlab.user::lang.plugin.name',
            'description' => 'rainlab.user::lang.plugin.description',
            'author' => 'Alexey Bobkov, Samuel Georges',
            'icon' => 'icon-user',
            'homepage' => 'https://github.com/rainlab/user-plugin'
        ];
    }

    /**
     * register
     */
    public function register()
    {
        $alias = AliasLoader::getInstance();
        $alias->alias('Auth', \RainLab\User\Facades\Auth::class);

        App::singleton('user.auth', function () {
            return \RainLab\User\Classes\AuthManager::instance();
        });

        // Overrides with our own extended version of Redirector to support
        // separate url.intended session variable for frontend
        App::singleton('redirect', function ($app) {
            $redirector = new UserRedirector($app['url']);

            // If the session is set on the application instance, we'll inject it into
            // the redirector instance. This allows the redirect responses to allow
            // for the quite convenient "with" methods that flash to the session.
            if (isset($app['session.store'])) {
                $redirector->setSession($app['session.store']);
            }

            return $redirector;
        });

        // Apply user-based mail blocking
        Event::listen('mailer.prepareSend', function ($mailer, $view, $message) {
            return MailBlocker::filterMessage($view, $message);
        });

        // Compatibility with RainLab.Notify
        $this->bindNotificationEvents();
    }

    /**
     * registerComponents
     */
    public function registerComponents()
    {
        return [
            \RainLab\User\Components\Session::class => 'session',
            \RainLab\User\Components\Account::class => 'account',
            \RainLab\User\Components\ResetPassword::class => 'resetPassword'
        ];
    }

    /**
     * registerPermissions
     */
    public function registerPermissions()
    {
        return [
            'rainlab.users.access_users' => [
                'tab' => 'rainlab.user::lang.plugin.tab',
                'label' => 'rainlab.user::lang.plugin.access_users'
            ],
            'rainlab.users.access_groups' => [
                'tab' => 'rainlab.user::lang.plugin.tab',
                'label' => 'rainlab.user::lang.plugin.access_groups'
            ],
            'rainlab.users.access_settings' => [
                'tab' => 'rainlab.user::lang.plugin.tab',
                'label' => 'rainlab.user::lang.plugin.access_settings'
            ],
            'rainlab.users.impersonate_user' => [
                'tab' => 'rainlab.user::lang.plugin.tab',
                'label' => 'rainlab.user::lang.plugin.impersonate_user'
            ],
        ];
    }

    /**
     * registerNavigation
     */
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

                'sideMenu' => [
                    'users' => [
                        'label' => 'rainlab.user::lang.users.menu_label',
                        'icon'        => 'icon-user',
                        'url'         => Backend::url('rainlab/user/users'),
                        'permissions' => ['rainlab.users.access_users']
                    ],
                    'usergroups' => [
                        'label'       => 'rainlab.user::lang.groups.menu_label',
                        'icon'        => 'icon-users',
                        'url'         => Backend::url('rainlab/user/usergroups'),
                        'permissions' => ['rainlab.users.access_groups']
                    ]
                ]
            ]
        ];
    }

    /**
     * registerSettings
     */
    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'rainlab.user::lang.settings.menu_label',
                'description' => 'rainlab.user::lang.settings.menu_description',
                'category'    => SettingsManager::CATEGORY_USERS,
                'icon'        => class_exists('System') ? 'octo-icon-user-actions-key' : 'icon-cog',
                'class'       => 'RainLab\User\Models\Settings',
                'order'       => 500,
                'permissions' => ['rainlab.users.access_settings']
            ]
        ];
    }

    /**
     * registerMailTemplates
     */
    public function registerMailTemplates()
    {
        return [
            'rainlab.user::mail.activate',
            'rainlab.user::mail.welcome',
            'rainlab.user::mail.restore',
            'rainlab.user::mail.new_user',
            'rainlab.user::mail.reactivate',
            'rainlab.user::mail.invite',
        ];
    }

    /**
     * registerNotificationRules
     */
    public function registerNotificationRules()
    {
        return [
            'groups' => [
                'user' => [
                    'label' => 'User',
                    'icon' => 'icon-user'
                ],
            ],
            'events' => [
                \RainLab\User\NotifyRules\UserActivatedEvent::class,
                \RainLab\User\NotifyRules\UserRegisteredEvent::class,
            ],
            'actions' => [],
            'conditions' => [
                \RainLab\User\NotifyRules\UserAttributeCondition::class
            ],
        ];
    }

    /**
     * bindNotificationEvents
     */
    protected function bindNotificationEvents()
    {
        if (!class_exists(Notifier::class)) {
            return;
        }

        Notifier::bindEvents([
            'rainlab.user.activate' => \RainLab\User\NotifyRules\UserActivatedEvent::class,
            'rainlab.user.register' => \RainLab\User\NotifyRules\UserRegisteredEvent::class
        ]);

        Notifier::instance()->registerCallback(function ($manager) {
            $manager->registerGlobalParams([
                'user' => Auth::getUser()
            ]);
        });
    }
}
