<?php namespace RainLab\User\Models\User;

use Str;
use Config;
use RainLab\User\Classes\TwoFactorManager;
use RainLab\User\Helpers\User as UserHelper;
use October\Rain\Support\Collection;
use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\Fill;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

/**
 * HasTwoFactor contract
 */
trait HasTwoFactor
{
    /**
     * enableTwoFactorAuthentication
     */
    public function enableTwoFactorAuthentication()
    {
        $secretKey = TwoFactorManager::instance()->generateSecretKey();

        $recoveryCodes = Collection::times(8, function() {
            return $this->generateRecoveryCode();
        })->all();

        $this->forceFill([
            'two_factor_secret' => $secretKey,
            'two_factor_recovery_codes' => json_encode($recoveryCodes),
        ]);

        $this->save();
    }

    /**
     * generateNewRecoveryCodes
     */
    public function generateNewRecoveryCodes()
    {
        $recoveryCodes = Collection::times(8, function() {
            return $this->generateRecoveryCode();
        })->all();

        $this->forceFill([
            'two_factor_recovery_codes' => json_encode($recoveryCodes),
        ]);

        $this->save();
    }

    /**
     * disableTwoFactorAuthentication
     */
    public function disableTwoFactorAuthentication()
    {
        if ($this->two_factor_secret !== null ||
            $this->two_factor_recovery_codes !== null ||
            $this->two_factor_confirmed_at !== null
        ) {
            $this->forceFill([
                'two_factor_secret' => null,
                'two_factor_recovery_codes' => null,
                'two_factor_confirmed_at' => null
            ]);

            $this->save();
        }
    }

    /**
     * @return bool hasEnabledTwoFactorAuthentication determines if two-factor authentication has been enabled.
     */
    public function hasEnabledTwoFactorAuthentication()
    {
        return $this->two_factor_secret !== null && $this->two_factor_confirmed_at !== null;
    }

    /**
     * recoveryCodes gets the user two factor authentication recovery codes.
     */
    public function recoveryCodes(): array
    {
        return json_decode($this->two_factor_recovery_codes, true);
    }

    /**
     * replaceRecoveryCode with a new one in the user's stored codes.
     */
    public function replaceRecoveryCode(string $code)
    {
        $this->forceFill([
            'two_factor_recovery_codes' => str_replace(
                $code,
                $this->generateRecoveryCode(),
                $this->two_factor_recovery_codes
            ),
        ]);

        $this->save();
    }

    /**
     * twoFactorQrCodeSvg gets the QR code SVG of the user's two factor authentication QR code URL.
     */
    public function twoFactorQrCodeSvg(): string
    {
        $url = $this->twoFactorQrCodeUrl();
        if (!$url) {
            return '';
        }

        $svg = (new Writer(
            new ImageRenderer(
                new RendererStyle(192, 0, null, null, Fill::uniformColor(new Rgb(255, 255, 255), new Rgb(45, 55, 72))),
                new SvgImageBackEnd
            )
        ))->writeString($url);

        return trim(substr($svg, strpos($svg, "\n") + 1));
    }

    /**
     * twoFactorQrCodeUrl gets the two factor authentication QR code URL.
     */
    public function twoFactorQrCodeUrl(): string
    {
        if (!$this->two_factor_secret) {
            return '';
        }

        return TwoFactorManager::instance()->qrCodeUrl(
            Config::Get('app.name'),
            $this->{UserHelper::username()},
            $this->two_factor_secret
        );
    }

    /**
     * generateRecoveryCode
     */
    public function generateRecoveryCode(): string
    {
        return Str::random(10) . '-' . Str::random(10);
    }
}
