<?php namespace RainLab\User\Classes\ActionManager;

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
     * login authenticates a user from the supplied credentials. Supported options:
     *
     * - remember: persist the user session with a cookie. Default: false.
     *
     * Returns a custom event response, or null.
     */
    public function login(array $input, array $options = [])
    {
        $remember = (bool) array_get($options, 'remember', false);

        $this->ensureLoginIsNotThrottled($input);

        if (($event = $this->fireBeforeAuthenticateEvent($input)) !== null) {
            if ($event === false || !$event instanceof Authenticatable) {
                $this->throwFailedAuthenticationException($input);
            }

            Auth::login($event, $remember);
        }
        elseif (!$this->attemptAuthentication($input, $remember)) {
            $this->throwFailedAuthenticationException($input);
        }

        $this->prepareAuthenticatedSession();

        // Trigger login event
        if ($user = Auth::user()) {
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
    protected function ensureLoginIsNotThrottled(array $input)
    {
        $limiter = $this->makeLoginRateLimiter($input);

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
    protected function attemptAuthentication(array $input, bool $remember): bool
    {
        $credentials = array_only($input, [UserHelper::username(), 'password']);

        Validator::make($input, [
            UserHelper::username() => 'required|string',
            'password' => 'required|string',
        ])->validate();

        return Auth::attempt($credentials, $remember);
    }

    /**
     * throwFailedAuthenticationException
     */
    protected function throwFailedAuthenticationException(array $input)
    {
        $this->makeLoginRateLimiter($input)->increment();

        throw new ValidationException([UserHelper::username() => __("These credentials do not match our records.")]);
    }

    /**
     * makeLoginRateLimiter
     */
    protected function makeLoginRateLimiter(array $input)
    {
        return new \System\Classes\RateLimiter('login:'.array_get($input, UserHelper::username()));
    }

    /**
     * fireBeforeAuthenticateEvent returns false if the authentication failed, a user object
     * if the authentication was successful (override), or null to do nothing.
     */
    protected function fireBeforeAuthenticateEvent(array $input)
    {
        /**
         * @event rainlab.user.beforeAuthenticate
         * Provides custom logic for logging in a user during authentication.
         *
         * Example usage:
         *
         *     Event::listen('rainlab.user.beforeAuthenticate', function ($component, $input) {
         *         return User::find(...);
         *     });
         *
         * Or
         *
         *     $component->bindEvent('user.beforeAuthenticate', function ($input) {
         *         return User::find(...);
         *     });
         *
         */
        return $this->fireSystemEvent('rainlab.user.beforeAuthenticate', [&$input]);
    }

    /**
     * fireAuthenticateEvent can return a custom response, or null to do nothing.
     */
    protected function fireAuthenticateEvent()
    {
        /**
         * @event rainlab.user.authenticate
         * Provides custom response logic after authentication
         *
         * Example usage:
         *
         *     Event::listen('rainlab.user.authenticate', function ($component) {
         *         // Fire logic
         *     });
         *
         * Or
         *
         *     $component->bindEvent('user.authenticate', function () {
         *         // Fire logic
         *     });
         *
         */
        return $this->fireSystemEvent('rainlab.user.authenticate');
    }
}
