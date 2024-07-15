<?php namespace RainLab\User;

use App;
use Event;
use Config;
use Backend;
use System\Classes\PluginBase;
use System\Classes\SettingsManager;
use RainLab\User\Classes\UserRedirector;
use RainLab\User\Classes\UserProvider;

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
            'name' => "User",
            'description' => "Front-end user management.",
            'author' => 'Alexey Bobkov, Samuel Georges',
            'icon' => 'icon-user',
            'homepage' => 'https://github.com/rainlab/user-plugin',
            'hint' => 'user'
        ];
    }

    /**
     * register the service provider.
     */
    public function register()
    {
        $this->registerAuthConfiguration();
        $this->registerSingletons();
        $this->registerAuthProvider();
        $this->registerCustomRedirector();
        $this->registerMailBlocker();

        $this->registerConsoleCommand('user.migratev1', \RainLab\User\Console\MigrateV1Command::class);
    }

    /**
     * boot the module events.
     */
    public function boot()
    {
    }

    /**
     * registerAuthConfiguration
     */
    protected function registerAuthConfiguration()
    {
        if (!Config::get('auth.defaults')) {
            Config::set('auth', Config::get('rainlab.user::auth'));
        }
    }

    /**
     * registerSingletons
     */
    protected function registerSingletons()
    {
        $this->app->singleton('user.twofactor', \RainLab\User\Classes\TwoFactorManager::class);

        // Laravel services
        $this->app->alias('auth', \RainLab\User\Classes\AuthManager::class);
        $this->app->alias('auth', \Illuminate\Contracts\Auth\Factory::class);
        $this->app->alias('auth.driver', \Illuminate\Contracts\Auth\Guard::class);

        $this->app->singleton('auth', fn ($app) => new \RainLab\User\Classes\AuthManager($app));
        $this->app->singleton('auth.driver', fn ($app) => $app['auth']->guard());
    }

    /**
     * registerAuthProvider extends the auth manager to include a custom provider
     */
    protected function registerAuthProvider()
    {
        $this->app->auth->provider('user', function ($app, array $config) {
            return new UserProvider($app['hash'], $config['model']);
        });
    }

    /**
     * registerCustomRedirector extends the redirector session state to use
     * a unique key for the frontend
     */
    protected function registerCustomRedirector()
    {
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
    }

    /**
     * registerMailBlocker applies user-based mail blocking
     */
    protected function registerMailBlocker()
    {
        Event::listen('mailer.prepareSend', function ($mailer, $view, $message) {
            return \RainLab\User\Models\UserPreference::filterMailMessage($view, $message);
        });
    }

    /**
     * registerComponents
     */
    public function registerComponents()
    {
        return [
            \RainLab\User\Components\Session::class => 'session',
            \RainLab\User\Components\Account::class => 'account',
            \RainLab\User\Components\ResetPassword::class => 'resetPassword',
            \RainLab\User\Components\Authentication::class => 'authentication',
            \RainLab\User\Components\Registration::class => 'registration',
        ];
    }

    /**
     * registerPermissions
     */
    public function registerPermissions()
    {
        return [
            'rainlab.users.access_users' => [
                'tab' => "Users",
                'label' => "Manage Users"
            ],
            'rainlab.users.access_groups' => [
                'tab' => "Users",
                'label' => "Manage User Groups"
            ],
            'rainlab.users.access_settings' => [
                'tab' => "Users",
                'label' => "Manage User Settings"
            ],
            'rainlab.users.impersonate_user' => [
                'tab' => "Users",
                'label' => "Impersonate Users"
            ],
            'rainlab.user.timelines' => [
                'tab' => "Users",
                'label' => "Manage Timelines"
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
                'label' => "Users",
                'url' => Backend::url('rainlab/user/users'),
                'icon' => 'icon-user',
                'iconSvg' => 'plugins/rainlab/user/assets/images/user-icon.svg',
                'permissions' => ['rainlab.users.*'],
                'order' => 500,

                'sideMenu' => [
                    'timelines' => [
                        'label' => "Activity",
                        'icon' => 'icon-bars',
                        'url' => Backend::url('user/timelines'),
                        'permissions' => ['rainlab.user.timelines']
                    ],
                    'users' => [
                        'label' => "Users",
                        'icon' => 'icon-user',
                        'url' => Backend::url('rainlab/user/users'),
                        'permissions' => ['rainlab.users.access_users']
                    ],
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
                'label' => "User Settings",
                'description' => "Manage user authentication, registration and activation settings.",
                'category' => SettingsManager::CATEGORY_USERS,
                'icon' => 'icon-user-actions-key',
                'class' => \RainLab\User\Models\Setting::class,
                'order' => 500,
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
            'user:invite_email' => 'rainlab.user::mail.invite_email',
            'user:welcome_email' => 'rainlab.user::mail.welcome_email',
            'user:recover_password' => 'rainlab.user::mail.recover_password',
            'user:verify_email' => 'rainlab.user::mail.verify_email',
            'user:new_user_internal' => 'rainlab.user::mail.new_user_internal',
        ];
    }
}
