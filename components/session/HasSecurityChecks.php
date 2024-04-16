<?php namespace RainLab\User\Components\Session;

use Cms;
use Auth;
use Request;
use Redirect;
use RainLab\User\Components\Session as SessionComponent;
use RainLab\User\Models\Setting as UserSetting;
use SystemException;


/**
 * HasSecurityChecks
 *
 * @package rainlab\user
 * @author Alexey Bobkov, Samuel Georges
 */
trait HasSecurityChecks
{
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
        if (!$this->checkUserSecurity()) {
            $userRedirect = $this->property('redirect');
            if (!$userRedirect) {
                throw new SystemException("The redirect property is empty on Session component.");
            }

            return Redirect::guest(Cms::pageUrl($userRedirect));
        }

        if (!$this->checkUserGroupSecurity()) {
            $groupRedirect = $this->property('redirectGroup', $this->property('redirect'));
            if (!$groupRedirect) {
                throw new SystemException("The redirectGroup property is empty on Session component.");
            }

            return Redirect::guest(Cms::pageUrl($groupRedirect));
        }

        if (!$this->checkUserVerifiedSecurity()) {
            $verifyRedirect = $this->property('redirectVerify', $this->property('redirect'));
            if (!$verifyRedirect) {
                throw new SystemException("The redirectVerify property is empty on Session component.");
            }

            return Redirect::guest(Cms::pageUrl($verifyRedirect));
        }
    }

    /**
     * checkUserSecurity checks if the user can access this page based on the security rules.
     */
    protected function checkUserSecurity(): bool
    {
        $allowedGroup = $this->property('security', SessionComponent::ALLOW_ALL);

        if (Auth::check()) {
            if ($allowedGroup == SessionComponent::ALLOW_GUEST) {
                return false;
            }
        }
        else {
            if ($allowedGroup == SessionComponent::ALLOW_USER) {
                return false;
            }
        }

        return true;
    }

    /**
     * checkUserGroupSecurity checks if the user can access this page based on their group
     */
    protected function checkUserGroupSecurity(): bool
    {
        $allowUserGroups = (array) $this->property('allowUserGroups', []);

        if (Auth::check() && $allowUserGroups) {
            $userGroups = $this->user()?->groups?->pluck('code')->all() ?? [];
            if (!count(array_intersect($allowUserGroups, $userGroups))) {
                return false;
            }
        }

        return true;
    }

    /**
     * checkUserGroupSecurity checks if the user is verified and if verification is required
     */
    protected function checkUserVerifiedSecurity(): bool
    {
        $requireActivation = UserSetting::get('require_activation', false);

        if (Auth::check() && $requireActivation) {
            if (!$this->user()?->hasVerifiedEmail()) {
                return false;
            }
        }

        return true;
    }
}
