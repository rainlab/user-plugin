<?php namespace RainLab\User\Components\Account;

use Carbon\Carbon;
use RainLab\User\Classes\TwoFactorManager;
use RainLab\User\Models\UserLog;
use ValidationException;
use ApplicationException;
use ForbiddenException;

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
            throw new ForbiddenException;
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
            throw new ForbiddenException;
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

        UserLog::createRecord($user->getKey(), UserLog::TYPE_SET_TWO_FACTOR, [
            'user_full_name' => $user->full_name,
            'is_two_factor_enabled' => true
        ]);
    }

    /**
     * actionDisableTwoFactor
     */
    protected function actionDisableTwoFactor()
    {
        $user = $this->user();

        if (!$user) {
            throw new ForbiddenException;
        }

        $user->disableTwoFactorAuthentication();

        UserLog::createRecord($user->getKey(), UserLog::TYPE_SET_TWO_FACTOR, [
            'user_full_name' => $user->full_name,
            'is_two_factor_enabled' => false
        ]);
    }
}
