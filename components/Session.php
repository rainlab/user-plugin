<?php namespace RainLab\User\Components;

use App;
use Cms;
use Auth;
use Flash;
use Cookie;
use Request;
use Redirect;
use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use RainLab\User\Models\User;
use SystemException;

/**
 * Session management component
 *
 * This will inject the user object to every page and provide the ability for
 * the user to sign out. This can also be used to restrict access to pages.
 *
 * @package rainlab\user
 * @author Alexey Bobkov, Samuel Georges
 */
class Session extends ComponentBase
{
    const ALLOW_ALL = 'all';
    const ALLOW_GUEST = 'guest';
    const ALLOW_USER = 'user';

    /**
     * componentDetails
     */
    public function componentDetails()
    {
        return [
            'name' => "Session",
            'description' => "Checks the user session and includes the user object on the page."
        ];
    }

    /**
     * defineProperties
     */
    public function defineProperties()
    {
        return [
            'security' => [
                'title' => "Security Mode",
                'description' => "Restricts access this page to either all users, logged in users, or logged out guests.",
                'type' => 'dropdown',
                'default' => 'all',
                'options' => [
                    'all' => "All",
                    'user' => "Users",
                    'guest' => "Guests"
                ]
            ],
            'redirect' => [
                'title' => "Default Redirect",
                'description' => "When access is denied, redirect to this CMS page.",
                'type' => 'dropdown',
                'group' => "Redirects",
                'default' => ''
            ],
            'checkToken' => [
                'title' => "Check Bearer Token (JWT)",
                'description' => "Check authentication using a verified bearer token.",
                'type' => 'checkbox',
                'default' => 0,
                'showExternalParam' => false
            ],
        ];
    }

    /**
     * getRedirectOptions
     */
    public function getRedirectOptions()
    {
        return [''=>'- none -'] + Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    /**
     * init component
     */
    public function init()
    {
        // Login with token
        if ($this->property('checkToken', false)) {
            $this->authenticateWithBearerToken();
        }

        // Authenticate session
        $this->authenticateSession();

        // Inject security logic pre-AJAX
        $this->registerAjaxSecurity();
    }

    /**
     * onRun executed when this component is bound to a page or layout.
     */
    public function onRun()
    {
        if ($redirect = $this->checkUserSecurityRedirect()) {
            return $redirect;
        }

        $this->page['user'] = $this->user();
    }

    /**
     * onLogout logs out the user
     *
     * Usage:
     *   <a data-request="onLogout">Sign out</a>
     *
     * With the optional redirect parameter:
     *   <a data-request="onLogout" data-request-data="redirect: '/good-bye'">Sign out</a>
     *
     */
    public function onLogout()
    {
        $user = Auth::user();

        if (Auth::isImpersonator()) {
            Auth::stopImpersonate();
        }
        else {
            Auth::logout();
            Request::session()->invalidate();
            Request::session()->regenerateToken();
        }

        if ($user) {
            /**
             * @event rainlab.user.logout
             * Provides custom response logic for logging out a user.
             *
             * Example usage:
             *
             *     Event::listen('rainlab.user.logout', function ($component, $user) {
             *         // Fire logic
             *     });
             *
             * Or
             *
             *     $component->bindEvent('user.logout', function ($user) {
             *         // Fire logic
             *     });
             *
             */
            if ($event = $this->fireSystemEvent('rainlab.user.logout', [$user])) {
                return $event;
            }
        }

        if ($flash = Cms::flashFromPost(__("You have been successfully logged out!"))) {
            Flash::success($flash);
        }

        if ($redirectUrl = post('redirect', Request::fullUrl())) {
            return Redirect::to($redirectUrl);
        }
    }

    /**
     * user returns the logged in user
     */
    public function user(): ?User
    {
        $user = Auth::user();

        if ($user && !$this->impersonator()) {
            $user->touchLastSeen();
        }

        return $user;
    }

    /**
     * registerAjaxSecurity injects security logic before AJAX
     */
    protected function registerAjaxSecurity()
    {
        $this->controller->bindEvent('page.init', function() {
            if (Request::ajax() && ($redirect = $this->checkUserSecurityRedirect())) {
                return ['X_OCTOBER_REDIRECT' => $redirect->getTargetUrl()];
            }
        });
    }

    /**
     * checkUserSecurityRedirect will return a redirect if the user cannot access the page.
     */
    protected function checkUserSecurityRedirect()
    {
        // No security layer enabled
        if ($this->checkUserSecurity()) {
            return;
        }

        if (!$this->property('redirect')) {
            throw new SystemException("The redirect property is empty on Session component.");
        }

        return Redirect::guest(
            Cms::pageUrl($this->property('redirect'))
        );
    }

    /**
     * checkUserSecurity checks if the user can access this page based on the security rules.
     */
    protected function checkUserSecurity(): bool
    {
        $allowedGroup = $this->property('security', self::ALLOW_ALL);

        if (Auth::check()) {
            if ($allowedGroup == self::ALLOW_GUEST) {
                return false;
            }
        }
        else {
            if ($allowedGroup == self::ALLOW_USER) {
                return false;
            }
        }

        return true;
    }

    //
    // JWT
    //

    /**
     * token returns an authentication token
     */
    public function token()
    {
        return Auth::getBearerToken();
    }

    /**
     * authenticateWithBearerToken
     */
    protected function authenticateWithBearerToken()
    {
        if ($jwtToken = Request::bearerToken()) {
            Auth::loginUsingBearerToken($jwtToken);
        }
    }

    //
    // Impersonation
    //

    /**
     * onStopImpersonating if impersonating, revert back to the previously signed in user.
     * @return Redirect
     */
    public function onStopImpersonating()
    {
        if (!$this->impersonator()) {
            return $this->onLogout();
        }

        Auth::stopImpersonate();

        $url = post('redirect', Request::fullUrl());

        if ($flash = Cms::flashFromPost(__("You are no longer impersonating a user."))) {
            Flash::success($flash);
        }

        return Redirect::to($url);
    }

    /**
     * impersonator returns the previously signed in user when impersonating.
     */
    public function impersonator()
    {
        return Auth::getImpersonator();
    }

    /**
     * authenticateSession is an adaptation of the AuthenticateSession middleware.
     * It checks if the password hash has changed and logs out the user. This is
     * an extra redundancy check and is already covered by the persist code.
     *
     * @see \Illuminate\Session\Middleware\AuthenticateSession
     */
    protected function authenticateSession()
    {
        if (!Request::hasSession() || !$this->user()) {
            return;
        }

        $logoutFunc = function() {
            Auth::logoutCurrentDevice();
            Request::session()->flush();
        };

        $user = Auth::getRealUser();
        $driver = Auth::getDefaultDriver();

        if (Auth::viaRemember()) {
            $passwordHash = explode('|', Cookie::get(Auth::getRecallerName()))[2] ?? null;
            if (!$passwordHash || $passwordHash != $user->getAuthPassword()) {
                $logoutFunc();
                return;
            }
        }

        if (!Request::session()->has("password_hash_{$driver}")) {
            Request::session()->put([
                "password_hash_{$driver}" => $user->getAuthPassword(),
            ]);
        }

        if (Request::session()->get("password_hash_{$driver}") !== $user->getAuthPassword()) {
            $logoutFunc();
            return;
        }

        App::after(function() use ($driver) {
            if ($user = Auth::getRealUser()) {
                Request::session()->put([
                    "password_hash_{$driver}" => $user->getAuthPassword(),
                ]);
            }
        });
    }
}
