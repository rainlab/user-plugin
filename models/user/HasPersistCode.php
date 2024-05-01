<?php namespace RainLab\User\Models\User;

use Str;

/**
 * HasPersistCode contract
 */
trait HasPersistCode
{
    /**
     * getPersistCode gets a code for when the user is persisted to a session, acting
     * as a checksum for a valid session. If the code changes, the user is logged out.
     * @return string
     */
    public function getPersistCode(): string
    {
        if ($this->persist_code) {
            return $this->persist_code;
        }

        $timestamps = $this->timestamps;

        $this->timestamps = false;

        $this->setPersistCode($code = $this->generatePersistCode());

        $this->save(['force' => true]);

        $this->timestamps = $timestamps;

        return $code;
    }

    /**
     * setPersistCode sets a persistence code
     */
    public function setPersistCode($code)
    {
        $this->persist_code = $code;
    }

    /**
     * generateRecoveryCode
     */
    public function generatePersistCode(): string
    {
        return Str::random(42);
    }
}
