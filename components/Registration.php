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

        Auth::login($user);

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

        // Redirect to the intended page after successful registration
        if ($redirect = Cms::redirectIntendedFromPost()) {
            return $redirect;
        }
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
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => UserHelper::passwordRules(),
        ])->validate();

        $user = User::create([
            'first_name' => $input['first_name'],
            'last_name' => $input['last_name'],
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
