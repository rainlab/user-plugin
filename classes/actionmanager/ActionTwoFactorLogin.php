<?php namespace RainLab\User\Classes\ActionManager;

use Auth;
use Event;
use Session;
use Validator;
use RainLab\User\Models\User;
use RainLab\User\Classes\TwoFactorManager;
use RainLab\User\Helpers\User as UserHelper;
use Illuminate\Contracts\Auth\Authenticatable;
use ValidationException;

/**
 * ActionTwoFactorLogin extends ActionLogin
 *
 * @mixin \RainLab\User\Classes\ActionManager\ActionLogin
 *
 * @package rainlab\user
 * @author Alexey Bobkov, Samuel Georges
 */
trait ActionTwoFactorLogin
{
    /**
     * @var mixed challengedUser attempting the two factor challenge.
     */
    protected $challengedUser;

    /**
     * loginWithTwoFactor authenticates a user, deferring to a two factor challenge when the
     * user has one set up. Supported options:
     *
     * - remember: persist the user session with a cookie. Default: false.
     *
     * Returns the TWO_FACTOR_CHALLENGE constant when a challenge has started,
     * a custom event response, or null.
     */
    public function loginWithTwoFactor(array $input, array $options = [])
    {
        $this->ensureLoginIsNotThrottled($input);

        if (($event = $this->fireBeforeAuthenticateEvent($input)) !== null) {
            if ($event === false || !$event instanceof Authenticatable) {
                $this->throwFailedAuthenticationException($input);
            }

            $user = $event;
        }
        else {
            $user = $this->attemptTwoFactorAuthentication($input);

            if (!$user) {
                $this->throwFailedAuthenticationException($input);
            }
        }

        // User does not have 2FA set up
        if (!$user->two_factor_secret || $user->two_factor_confirmed_at === null) {
            return $this->login($input, $options);
        }

        Session::put('login.id', $user->getKey());
        Session::put('login.remember', (bool) array_get($options, 'remember', false));

        return static::TWO_FACTOR_CHALLENGE;
    }

    /**
     * twoFactorChallenge completes a login using a two factor or recovery code. Supported options:
     *
     * - remember: persist the user session with a cookie. Default: false.
     *
     * Returns a custom event response, or null.
     */
    public function twoFactorChallenge(array $input, array $options = [])
    {
        $user = $this->getChallengedUser();

        if ($code = $this->getValidRecoveryCode($input)) {
            $user->replaceRecoveryCode($code);
        }
        elseif (!$this->hasValidCode($input)) {
            $this->throwFailedTwoFactorException($input);
        }

        Auth::login($user, (bool) array_get($options, 'remember', false));

        $this->prepareAuthenticatedSession();

        Event::fire('rainlab.user.login', [$user]);

        $this->recordUserLogAuthenticated($user, true);

        if ($event = $this->fireAuthenticateEvent()) {
            return $event;
        }
    }

    /**
     * hasChallengedUser determines if there is a challenged user in the current session.
     */
    public function hasChallengedUser(): bool
    {
        if ($this->challengedUser) {
            return true;
        }

        $model = $this->getUserModel();

        return Session::has('login.id') && $model->find(Session::get('login.id'));
    }

    /**
     * getChallengedUser gets the user that is attempting the two factor challenge.
     */
    protected function getChallengedUser()
    {
        if ($this->challengedUser) {
            return $this->challengedUser;
        }

        $model = $this->getUserModel();

        if (
            !Session::has('login.id') ||
            !($user = $model->find(Session::get('login.id')))
        ) {
            $this->throwFailedTwoFactorException([]);
        }

        return $this->challengedUser = $user;
    }

    /**
     * getValidRecoveryCode if one exists on the input.
     */
    protected function getValidRecoveryCode(array $input): ?string
    {
        $recoveryCode = array_get($input, 'recovery_code');
        if (!$recoveryCode || !is_string($recoveryCode)) {
            return null;
        }

        $code = collect($this->getChallengedUser()->recoveryCodes())
            ->first(function ($code) use ($recoveryCode) {
                return hash_equals($code, $recoveryCode) ? $code : null;
            })
        ;

        if ($code) {
            Session::forget('login.id');
        }

        return $code;
    }

    /**
     * hasValidCode determines if the input has a valid two factor code.
     */
    protected function hasValidCode(array $input): bool
    {
        $code = array_get($input, 'code');
        if (!$code || !is_string($code)) {
            return false;
        }

        $user = $this->getChallengedUser();

        $result = TwoFactorManager::instance()->verify($user->two_factor_secret, $code);

        if ($result) {
            Session::forget('login.id');
        }

        return $result;
    }

    /**
     * getUserModel
     */
    protected function getUserModel(): User
    {
        $className = Auth::getProvider()->getModel();

        return new $className;
    }

    /**
     * attemptTwoFactorAuthentication
     */
    protected function attemptTwoFactorAuthentication(array $input): ?Authenticatable
    {
        $credentials = array_only($input, [UserHelper::username(), 'password']);

        Validator::make($input, [
            UserHelper::username() => 'required|string',
            'password' => 'required|string',
        ])->validate();

        $user = $this->getUserModel()
            ->applyRegistered()
            ->where(UserHelper::username(), $credentials[UserHelper::username()])
            ->first()
        ;

        if (!$user) {
            return null;
        }

        if (!Auth::getProvider()->validateCredentials($user, [
            'password' => $credentials['password']
        ])) {
            return null;
        }

        return $user;
    }

    /**
     * throwFailedTwoFactorException
     */
    protected function throwFailedTwoFactorException(array $input)
    {
        if (array_get($input, 'recovery_code')) {
            throw new ValidationException(['recovery_code' => __("The provided two factor recovery code was invalid.")]);
        }

        throw new ValidationException(['code' => __("The provided two factor authentication code was invalid.")]);
    }
}
