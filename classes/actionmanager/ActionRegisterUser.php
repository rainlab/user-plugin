<?php namespace RainLab\User\Classes\ActionManager;

use Auth;
use Validator;
use RainLab\User\Models\User;
use RainLab\User\Models\Setting;
use RainLab\User\Models\UserLog;
use RainLab\User\Helpers\User as UserHelper;
use NotFoundException;

/**
 * ActionRegisterUser
 *
 * @package rainlab\user
 * @author Alexey Bobkov, Samuel Georges
 */
trait ActionRegisterUser
{
    /**
     * registerUser creates a new user and signs them in, unless an activation policy
     * defers the sign in. Supported options:
     *
     * - verifyUrl: an absolute URL to use in the verification email instead of the CMS entry point.
     *
     * Returns the registered user, or a custom event response.
     */
    public function registerUser(array $input, array $options = [])
    {
        if (!Setting::get('allow_registration')) {
            throw new NotFoundException;
        }

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

        // Approval requires an administrator to approve the user
        if (Setting::get('require_approval', false)) {
            $user->unapprove();
        }

        // Email verification sends a link to confirm the email address
        if (Setting::get('activation_email', false) && !$user->hasVerifiedEmail()) {
            if ($verifyUrl = array_get($options, 'verifyUrl')) {
                $user->setUrlForEmailVerification($verifyUrl);
            }

            $user->sendEmailVerificationNotification();
        }

        // Sign the user in immediately, unless an activation policy defers it
        if ($this->canSignInAfterRegister($user)) {
            Auth::login($user);
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

        return $user;
    }

    /**
     * canSignInAfterRegister returns true when the activation policy allows the
     * user to be signed in immediately after registering.
     */
    public function canSignInAfterRegister(User $user): bool
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
}
