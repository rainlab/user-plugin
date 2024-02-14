<?php namespace RainLab\User\Components\Account;

use Carbon\Carbon;
use RainLab\User\Classes\TwoFactorManager;
use ValidationException;
use ApplicationException;

/**
 * ActionTwoFactor
 *
 * @package rainlab\user
 * @author Alexey Bobkov, Samuel Georges
 */
trait ActionTwoFactor
{
    /**
     * fetchTwoFactorEnabled
     */
    protected function fetchTwoFactorEnabled(): bool
    {
        $user = $this->user();

        return $user && $user->hasEnabledTwoFactorAuthentication();
    }

    /**
     * fetchTwoFactorRecoveryCodes
     */
    protected function fetchTwoFactorRecoveryCodes(): array
    {
        $user = $this->user();

        if (!$user) {
            return [];
        }

        return json_decode($user->two_factor_recovery_codes, true) ?: [];
    }

    /**
     * actionEnableTwoFactor
     */
    protected function actionEnableTwoFactor()
    {
        $user = $this->user();

        if (!$user) {
            throw new ApplicationException(__("User not found"));
        }

        $user->enableTwoFactorAuthentication();
    }

    /**
     * actionRegenerateTwoFactorRecoveryCodes
     */
    protected function actionRegenerateTwoFactorRecoveryCodes()
    {
        $user = $this->user();

        if (!$user) {
            throw new ApplicationException(__("User not found"));
        }

        $user->generateNewRecoveryCodes();
    }

    /**
     * actionConfirmTwoFactor
     */
    protected function actionConfirmTwoFactor()
    {
        $user = $this->user();
        $code = post('code');

        if (
            !$user ||
            !$user->two_factor_secret ||
            !$code ||
            !TwoFactorManager::instance()->verify($user->two_factor_secret, $code)
        ) {
            throw new ValidationException([
                'code' => [__('The provided two factor authentication code was invalid.')],
            ]);
        }

        $user->forceFill([
            'two_factor_confirmed_at' => Carbon::now()
        ]);

        $user->save();
    }

    /**
     * actionDisableTwoFactor
     */
    protected function actionDisableTwoFactor()
    {
        $user = $this->user();

        if (!$user) {
            throw new ApplicationException(__("User not found"));
        }

        $user->disableTwoFactorAuthentication();
    }
}
