<?php namespace RainLab\User\Classes;

use App;
use Cache;
use Config;
use PragmaRX\Google2FA\Google2FA;

/**
 * TwoFactorManager
 *
 * @package rainlab\user
 * @author Alexey Bobkov, Samuel Georges
 */
class TwoFactorManager
{
    /**
     * @var \PragmaRX\Google2FA\Google2FA engine providing two factor authentication helper services.
     */
    protected $engine;

    /**
     * __construct manager
     */
    public function __construct()
    {
        $this->engine = new Google2FA;
    }

    /**
     * instance creates a new instance of this singleton
     */
    public static function instance(): static
    {
        return App::make('user.twofactor');
    }

    /**
     * generateSecretKey
     */
    public function generateSecretKey(): string
    {
        return $this->engine->generateSecretKey();
    }

    /**
     * qrCodeUrl returns the two factor authentication QR code URL.
     */
    public function qrCodeUrl(string $companyName, string $companyEmail, string $secret): string
    {
        return $this->engine->getQRCodeUrl($companyName, $companyEmail, $secret);
    }

    /**
     * verify the given code.
     */
    public function verify(string $secret, string $code): bool
    {
        if (is_int($customWindow = Config::get('user.twofactor.window'))) {
            $this->engine->setWindow($customWindow);
        }

        $timestamp = $this->engine->verifyKeyNewer(
            $secret, $code, Cache::get($key = 'user.2fa_codes.'.md5($code))
        );

        if ($timestamp !== false) {
            if ($timestamp === true) {
                $timestamp = $this->engine->getTimestamp();
            }

            Cache::put($key, $timestamp, ($this->engine->getWindow() ?: 1) * 60);

            return true;
        }

        return false;
    }
}
