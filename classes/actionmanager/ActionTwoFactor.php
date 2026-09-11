<?php namespace RainLab\User\Classes\ActionManager;

use Carbon\Carbon;
use RainLab\User\Classes\TwoFactorManager;
use RainLab\User\Models\UserLog;
use ValidationException;
use ForbiddenException;

/**
 * ActionTwoFactor manages the two factor authentication settings for a user
 *
 * @package rainlab\user
 * @author Alexey Bobkov, Samuel Georges
 */
trait ActionTwoFactor
{
    /**
     * hasTwoFactorEnabled returns true when the authenticated user has two factor set up
     */
    public function hasTwoFactorEnabled(): bool
    {
        $user = $this->user();

        return $user && $user->hasEnabledTwoFactorAuthentication();
    }

    /**
     * getTwoFactorRecoveryCodes returns the recovery codes for the authenticated user
     */
    public function getTwoFactorRecoveryCodes(): array
    {
        $user = $this->user();

        if (!$user) {
            return [];
        }

        return json_decode($user->two_factor_recovery_codes, true) ?: [];
    }

    /**
     * enableTwoFactor generates a two factor secret for the authenticated user,
     * pending confirmation
     */
    public function enableTwoFactor(): void
    {
        $user = $this->user();

        if (!$user) {
            throw new ForbiddenException;
        }

        $user->enableTwoFactorAuthentication();
    }

    /**
     * regenerateTwoFactorRecoveryCodes for the authenticated user
     */
    public function regenerateTwoFactorRecoveryCodes(): void
    {
        $user = $this->user();

        if (!$user) {
            throw new ForbiddenException;
        }

        $user->generateNewRecoveryCodes();
    }

    /**
     * confirmTwoFactor verifies a two factor code to complete the set up
     */
    public function confirmTwoFactor(array $input): void
    {
        $user = $this->user();
        $code = array_get($input, 'code');

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
     * disableTwoFactor for the authenticated user
     */
    public function disableTwoFactor(): void
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
