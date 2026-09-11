<?php namespace RainLab\User\Components;

use Cms;
use RainLab\User\Models\User;
use RainLab\User\Models\Setting;
use RainLab\User\Classes\ActionManager;
use Cms\Classes\ComponentBase;
use NotFoundException;

/**
 * Registration displays registration forms
 */
class Registration extends ComponentBase
{
    /**
     * componentDetails
     */
    public function componentDetails()
    {
        return [
            'name' => "Registration",
            'description' => "Provides services for registering a user.",
            'ajaxPartial' => true
        ];
    }

    /**
     * defineProperties
     */
    public function defineProperties()
    {
        return [
            'redirect' => [
                'title' => "Redirect To",
                'description' => "Page name to redirect to after registration, unless overridden by the form.",
                'type' => 'dropdown',
                'default' => ''
            ],
        ];
    }

    /**
     * getRedirectOptions
     */
    public function getRedirectOptions()
    {
        return [''=>'- none -'] + \Cms\Classes\Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    /**
     * onRegister
     */
    public function onRegister()
    {
        if (!$this->canRegister()) {
            throw new NotFoundException;
        }

        $result = $this->actions()->registerUser(post());
        if (!$result instanceof User) {
            return $result;
        }

        $user = $result;

        // Sign in deferred by an activation policy, inform the markup why
        if (!$this->actions()->canSignInAfterRegister($user)) {
            $this->page['awaitingActivation'] = Setting::get('require_activation', false) && !$user->hasVerifiedEmail();
            $this->page['awaitingApproval'] = Setting::get('require_approval', false) && $user->isPendingApproval();
            return;
        }

        // Redirect to the intended page after successful registration,
        // falling back to the component's redirect property
        if ($redirect = Cms::redirectIntendedFromPost($this->makeRedirectUrl())) {
            return $redirect;
        }
    }

    /**
     * makeRedirectUrl resolves the redirect property to a URL, or null when unset
     */
    protected function makeRedirectUrl(): ?string
    {
        if (!$page = $this->property('redirect')) {
            return null;
        }

        return Cms::pageUrl($page);
    }

    /**
     * canRegister checks if the registration is allowed
     */
    public function canRegister(): bool
    {
        return Setting::get('allow_registration');
    }

    /**
     * actions returns user workflow services hosted by this component
     */
    protected function actions(): ActionManager
    {
        return ActionManager::instance()->withContext($this);
    }
}
