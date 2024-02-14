<?php namespace RainLab\User\Components;

use Cms;
use Auth;
use Validator;
use RainLab\User\Models\User;
use RainLab\User\Helpers\User as UserHelper;
use Cms\Classes\ComponentBase;

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
        $input = post();

        /**
         * @event user.registration.create
         * Provides custom logic for creating a new user during registration.
         *
         * Example usage:
         *
         *     Event::listen('user.registration.create', function ($input) {
         *         return User::create(...);
         *     });
         *
         * Or
         *
         *     $component->bindEvent('registration.create', function ($input) {
         *         return User::create(...);
         *     });
         *
         */
        if ($event = $this->fireSystemEvent('user.registration.create', [&$input])) {
            $user = $event;
        }
        else {
            $user = $this->createNewUser($input);
        }

        Auth::login($user);

        /**
         * @event user.registration.response
         * Provides custom logic for creating a new user during registration.
         *
         * Example usage:
         *
         *     Event::listen('user.registration.response', function ($user) {
         *         // Fire logic
         *     });
         *
         * Or
         *
         *     $component->bindEvent('registration.response', function ($user) {
         *         // Fire logic
         *     });
         *
         */
        if ($event = $this->fireSystemEvent('user.registration.response', [$user])) {
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
            'email' => ['required', 'string', 'email', 'max:255', 'unique:user_users'],
            'password' => UserHelper::passwordRules(),
        ])->validate();

        return User::create([
            'first_name' => $input['first_name'],
            'last_name' => $input['last_name'],
            'email' => $input['email'],
            'password' => $input['password'],
        ]);
    }
}
