<?php namespace RainLab\User\Components;

use Cms;
use Auth;
use Event;
use Validator;
use RainLab\User\Models\User;
use RainLab\User\Models\Setting;
use RainLab\User\Models\UserLog;
use RainLab\User\Helpers\User as UserHelper;
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
            'description' => "Provides services for registering a user."
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

        $input = post();

        /**
         * @event rainlab.user.beforeRegister
         * Provides custom logic for creating a new user during registration.
         *
         * Example usage:
         *
         *     Event::listen('rainlab.user.beforeRegister', function ($component, &$input) {
         *         return User::create(...);
         *     });
         *
         * Or
         *
         *     $component->bindEvent('user.beforeRegister', function (&$input) {
         *         return User::create(...);
         *     });
         *
         */
        if ($event = $this->fireSystemEvent('rainlab.user.beforeRegister', [&$input])) {
            $user = $event;
        }
        else {
            $user = $this->createNewUser($input);
        }

        $requireActivation = Setting::get('require_activation', false);
        $requireApproval = Setting::get('require_approval', false);

        // Approval requires an administrator to approve the user
        if ($requireApproval) {
            $user->unapprove();
        }

        // Email verification sends a link to confirm the email address
        if (Setting::get('activation_email', false) && !$user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
        }

        // Sign the user in immediately, unless an activation policy defers it
        $canSignIn = $this->canSignInAfterRegister($user);
        if ($canSignIn) {
            Auth::login($user);
        }
        else {
            // Inform the markup why the user is not signed in, based on which activation policies are active
            $this->page['awaitingActivation'] = $requireActivation && !$user->hasVerifiedEmail();
            $this->page['awaitingApproval'] = $requireApproval && $user->isPendingApproval();
        }

        /**
         * @event rainlab.user.register
         * Modify the return response after registration.
         *
         * Example usage:
         *
         *     Event::listen('rainlab.user.register', function ($component, $user) {
         *         // Fire logic
         *     });
         *
         * Or
         *
         *     $component->bindEvent('user.register', function ($user) {
         *         // Fire logic
         *     });
         *
         */
        if ($event = $this->fireSystemEvent('rainlab.user.register', [$user])) {
            return $event;
        }

        // Redirect to the intended page after successful registration,
        // falling back to the component's redirect property
        if ($canSignIn && ($redirect = Cms::redirectIntendedFromPost($this->makeRedirectUrl()))) {
            return $redirect;
        }
    }

    /**
     * canSignInAfterRegister returns true when the activation policy allows the
     * user to be signed in immediately after registering.
     */
    protected function canSignInAfterRegister(User $user): bool
    {
        if (Setting::get('require_activation', false) && !$user->hasVerifiedEmail()) {
            return false;
        }

        if (Setting::get('require_approval', false) && $user->isPendingApproval()) {
            return false;
        }

        return true;
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
     * createNewUser implements the logic for creating a new user
     */
    protected function createNewUser(array $input): User
    {
        // If the password confirmation field is absent from the request payload,
        // skip it here for a smoother registration process. Every second counts!
        if (!array_key_exists('password_confirmation', $input)) {
            $input['password_confirmation'] = $input['password'] ?? '';
        }

        Validator::make($input, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,NULL,id,is_guest,!1'],
            'password' => UserHelper::passwordRules(),
        ])->validate();

        $user = User::create([
            'first_name' => $input['first_name'],
            'last_name' => $input['last_name'] ?? null,
            'email' => $input['email'],
            'password' => $input['password'],
            'password_confirmation' => $input['password_confirmation'],
        ]);

        UserLog::createRecord($user->getKey(), UserLog::TYPE_NEW_USER, [
            'user_full_name' => $user->full_name,
        ]);

        return $user;
    }

    /**
     * canRegister checks if the registration is allowed
     */
    public function canRegister(): bool
    {
        return Setting::get('allow_registration');
    }
}
