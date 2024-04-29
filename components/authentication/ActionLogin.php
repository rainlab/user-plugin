<?php namespace RainLab\User\Components\Authentication;

use Auth;
use Event;
use Validator;
use RainLab\User\Helpers\User as UserHelper;
use ValidationException;

/**
 * ActionLogin
 *
 * @package rainlab\user
 * @author Alexey Bobkov, Samuel Georges
 */
trait ActionLogin
{
    /**
     * actionLogin
     */
    protected function actionLogin()
    {
        $this->ensureLoginIsNotThrottled();

        $input = post();

        Event::fire('rainlab.user.beforeAuthenticate', [$this, $input]);

        /**
         * @event rainlab.user.authenticateUser
         * Provides custom logic for logging in a user during authentication.
         *
         * Example usage:
         *
         *     Event::listen('rainlab.user.authenticateUser', function ($input) {
         *         return User::find(...);
         *     });
         *
         * Or
         *
         *     $component->bindEvent('user.authenticateUser', function ($input) {
         *         return User::find(...);
         *     });
         *
         */
        if ($event = $this->fireSystemEvent('rainlab.user.authenticateUser', [&$input])) {
            if ($event === false) {
                $this->throwFailedAuthenticationException();
            }

            Auth::login($event, $this->useRememberMe());
        }
        elseif (!$this->attemptAuthentication($input)) {
            $this->throwFailedAuthenticationException();
        }

        $this->prepareAuthenticatedSession();

        // Trigger login event
        if ($user = Auth::getUser()) {
            Event::fire('rainlab.user.login', [$user]);

            $this->recordUserLogAuthenticated($user);
        }

        /**
         * @event rainlab.user.authenticationResponse
         * Provides custom response logic after authentication
         *
         * Example usage:
         *
         *     Event::listen('rainlab.user.authenticationResponse', function () {
         *         // Fire logic
         *     });
         *
         * Or
         *
         *     $component->bindEvent('user.authenticationResponse', function () {
         *         // Fire logic
         *     });
         *
         */
        if ($event = $this->fireSystemEvent('rainlab.user.authenticationResponse')) {
            return $event;
        }
    }

    /**
     * ensureLoginIsNotThrottled
     */
    protected function ensureLoginIsNotThrottled()
    {
        $limiter = $this->makeLoginRateLimiter();

        if (!$limiter->tooManyAttempts()) {
            return;
        }

        /**
         * @event rainlab.user.authenticationLockout
         * Provides custom logic when a login attempt has been rate limited.
         *
         * Example usage:
         *
         *     Event::listen('rainlab.user.authenticationLockout', function () {
         *         // ...
         *     });
         *
         * Or
         *
         *     $component->bindEvent('user.authenticationLockout', function () {
         *         // ...
         *     });
         *
         */
        $this->fireSystemEvent('rainlab.user.authenticationLockout');

        $seconds = $limiter->availableIn();

        $message = __("Too many login attempts. Please try again in :seconds seconds.", [
            'seconds' => $seconds,
            'minutes' => ceil($seconds / 60),
        ]);

        throw new ValidationException([UserHelper::username() => $message]);
    }

    /**
     * attemptAuthentication
     */
    protected function attemptAuthentication(array $input): bool
    {
        $credentials = array_only($input, [UserHelper::username(), 'password']);

        Validator::make($input, [
            UserHelper::username() => 'required|string',
            'password' => 'required|string',
        ])->validate();

        return Auth::attempt($credentials, $this->useRememberMe());
    }

    /**
     * throwFailedAuthenticationException
     */
    protected function throwFailedAuthenticationException()
    {
        $this->makeLoginRateLimiter()->increment();

        throw new ValidationException([UserHelper::username() => __("These credentials do not match our records.")]);
    }

    /**
     * makeLoginRateLimiter
     */
    protected function makeLoginRateLimiter()
    {
        return new \System\Classes\RateLimiter('login:'.input(UserHelper::username()));
    }
}
