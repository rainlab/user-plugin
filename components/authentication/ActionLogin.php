<?php namespace RainLab\User\Components\Authentication;

use Auth;
use Request;
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

        /**
         * @event user.authentication.login
         * Provides custom logic for logging in a user during authentication.
         *
         * Example usage:
         *
         *     Event::listen('user.authentication.login', function ($input) {
         *         return User::find(...);
         *     });
         *
         * Or
         *
         *     $component->bindEvent('authentication.login', function ($input) {
         *         return User::find(...);
         *     });
         *
         */
        if ($event = $this->fireSystemEvent('user.authentication.login', [&$input])) {
            if ($event === false) {
                $this->throwFailedAuthenticationException();
            }

            Auth::login($event, $this->useRememberMe());
        }
        elseif (!$this->attemptAuthentication($input)) {
            $this->throwFailedAuthenticationException();
        }

        $this->prepareAuthenticatedSession();

        /**
         * @event user.registration.response
         * Provides custom logic for creating a new user during registration.
         *
         * Example usage:
         *
         *     Event::listen('user.registration.response', function () {
         *         // Fire logic
         *     });
         *
         * Or
         *
         *     $component->bindEvent('registration.response', function () {
         *         // Fire logic
         *     });
         *
         */
        if ($event = $this->fireSystemEvent('user.authentication.response')) {
            return $event;
        }
    }

    /**
     * useRememberMe checks if the remember me checkbox is shown, otherwise defaults to true
     */
    protected function useRememberMe(): bool
    {
        if ($this->useRememberMe()) {
            return (bool) input('remember');
        }

        return true;
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
         * @event user.authentication.lockout
         * Provides custom logic when a login attempt has been rate limited.
         *
         * Example usage:
         *
         *     Event::listen('user.authentication.lockout', function () {
         *         // ...
         *     });
         *
         * Or
         *
         *     $component->bindEvent('authentication.lockout', function () {
         *         // ...
         *     });
         *
         */
        $this->fireSystemEvent('user.authentication.lockout');

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
     * prepareAuthenticatedSession
     */
    protected function prepareAuthenticatedSession()
    {
        if (Request::hasSession()) {
            Request::session()->regenerate();
        }
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
