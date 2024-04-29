<?php namespace RainLab\User\Components\Authentication;

use Auth;
use Event;
use Session;
use Request;
use Redirect;
use RainLab\User\Models\User;
use RainLab\User\Classes\TwoFactorManager;
use RainLab\User\Helpers\User as UserHelper;
use ValidationException;

/**
 * ActionTwoFactorLogin extends ActionLogin
 *
 * @mixin \RainLab\User\Components\Authentication\ActionLogin
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
     * actionLoginWithTwoFactor
     */
    protected function actionLoginWithTwoFactor()
    {
        $this->ensureLoginIsNotThrottled();

        Request::validate([
            UserHelper::username() => 'required|string',
            'password' => 'required|string',
        ]);

        $user = $this->getUserModel()
            ->where(UserHelper::username(), post(UserHelper::username()))
            ->first()
        ;

        if (!$user || !Auth::getProvider()->validateCredentials($user, ['password' => post('password')])) {
            $this->throwFailedAuthenticationException();
        }

        // User does not have 2FA set up
        if (!$user->two_factor_secret || $user->two_factor_confirmed_at === null) {
            return $this->actionLogin();
        }

        Session::put('login.id', $user->getKey());
        Session::put('login.remember', $this->useRememberMe());

        return Redirect::to(Request::fullUrlWithQuery([
            'two-factor' => 'challenge'
        ]));
    }

    /**
     * actionTwoFactorChallenge
     */
    protected function actionTwoFactorChallenge()
    {
        $user = $this->getChallengedUser();

        if ($code = $this->getValidRecoveryCode()) {
            $user->replaceRecoveryCode($code);
        }
        elseif (!$this->hasValidCode()) {
            $this->throwFailedTwoFactorException();
        }

        Auth::login($user, $this->useRememberMe());

        $this->prepareAuthenticatedSession();

        Event::fire('rainlab.user.login', [$user]);

        $this->recordUserLogAuthenticated($user, true);
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
            $this->throwFailedTwoFactorException();
        }

        return $this->challengedUser = $user;
    }

    /**
     * hasChallengedUser determines if there is a challenged user in the current session.
     */
    protected function hasChallengedUser(): bool
    {
        if ($this->challengedUser) {
            return true;
        }

        $model = $this->getUserModel();

        return Session::has('login.id') && $model->find(Session::get('login.id'));
    }

    /**
     * getValidRecoveryCode if one exists on the request.
     */
    protected function getValidRecoveryCode(): ?string
    {
        $recoveryCode = post('recovery_code');
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
     * hasValidCode determines if the request has a valid two factor code.
     */
    protected function hasValidCode(): bool
    {
        $code = post('code');
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
     * throwFailedTwoFactorException
     */
    protected function throwFailedTwoFactorException()
    {
        if (post('recovery_code')) {
            throw new ValidationException(['recovery_code' => __("The provided two factor recovery code was invalid.")]);
        }

        throw new ValidationException(['code' => __("The provided two factor authentication code was invalid.")]);
    }
}
