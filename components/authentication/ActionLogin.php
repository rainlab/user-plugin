<?php namespace RainLab\User\Components\Authentication;

use Auth;
use Event;
use Validator;
use RainLab\User\Helpers\User as UserHelper;
use Illuminate\Contracts\Auth\Authenticatable;
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

        if (($event = $this->fireBeforeAuthenticateEvent()) !== null) {
            if ($event === false || !$event instanceof Authenticatable) {
                $this->throwFailedAuthenticationException();
            }

            Auth::login($event, $this->useRememberMe());
        }
        elseif (!$this->attemptAuthentication(post())) {
            $this->throwFailedAuthenticationException();
        }

        $this->prepareAuthenticatedSession();

        // Trigger login event
        if ($user = Auth::getUser()) {
            Event::fire('rainlab.user.login', [$user]);

            $this->recordUserLogAuthenticated($user);
        }

        if ($event = $this->fireAuthenticateEvent()) {
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
         * @event rainlab.user.lockout
         * Provides custom logic when a login attempt has been rate limited.
         *
         * Example usage:
         *
         *     Event::listen('rainlab.user.lockout', function () {
         *         // ...
         *     });
         *
         * Or
         *
         *     $component->bindEvent('user.lockout', function () {
         *         // ...
         *     });
         *
         */
        $this->fireSystemEvent('rainlab.user.lockout');

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
        return new \System\Classes\RateLimiter('login:'.post(UserHelper::username()));
    }
}
