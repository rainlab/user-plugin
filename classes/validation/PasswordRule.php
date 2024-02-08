<?php namespace RainLab\User\Classes\Validation;

use Str;
use Illuminate\Contracts\Validation\Rule;

/**
 * PasswordRule
 */
class PasswordRule implements Rule
{
    /**
     * @var int length for the password at a minimum
     */
    protected $length = 8;

    /**
     * @var bool requireUppercase indicates if the password must contain one uppercase character.
     */
    protected $requireUppercase = true;

    /**
     * @var bool requireNumeric indicates if the password must contain one numeric digit.
     */
    protected $requireNumeric = true;

    /**
     * @var bool requireSpecialCharacter indicates if the password must contain one special character.
     */
    protected $requireSpecialCharacter = true;

    /**
     * @var string message that should be used when validation fails.
     */
    protected $message;

    /**
     * passes determines if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $value = is_scalar($value) ? (string) $value : '';

        if ($this->requireUppercase && Str::lower($value) === $value) {
            return false;
        }

        if ($this->requireNumeric && ! preg_match('/[0-9]/', $value)) {
            return false;
        }

        if ($this->requireSpecialCharacter && ! preg_match('/[\W_]/', $value)) {
            return false;
        }

        return Str::length($value) >= $this->length;
    }

    /**
     * message gets the validation error message.
     * @return string
     */
    public function message()
    {
        if ($this->message) {
            return $this->message;
        }

        switch (true) {
            case $this->requireUppercase && !$this->requireNumeric && !$this->requireSpecialCharacter:
                return __('The :attribute must be at least :length characters and contain at least one uppercase character.', [
                    'length' => $this->length,
                ]);

            case $this->requireNumeric && !$this->requireUppercase && !$this->requireSpecialCharacter:
                return __('The :attribute must be at least :length characters and contain at least one number.', [
                    'length' => $this->length,
                ]);

            case $this->requireSpecialCharacter && !$this->requireUppercase && !$this->requireNumeric:
                return __('The :attribute must be at least :length characters and contain at least one special character.', [
                    'length' => $this->length,
                ]);

            case $this->requireUppercase && $this->requireNumeric && !$this->requireSpecialCharacter:
                return __('The :attribute must be at least :length characters and contain at least one uppercase character and one number.', [
                    'length' => $this->length,
                ]);

            case $this->requireUppercase && $this->requireSpecialCharacter && !$this->requireNumeric:
                return __('The :attribute must be at least :length characters and contain at least one uppercase character and one special character.', [
                    'length' => $this->length,
                ]);

            case $this->requireUppercase && $this->requireNumeric && $this->requireSpecialCharacter:
                return __('The :attribute must be at least :length characters and contain at least one uppercase character, one number, and one special character.', [
                    'length' => $this->length,
                ]);

            case $this->requireNumeric && $this->requireSpecialCharacter && !$this->requireUppercase:
                return __('The :attribute must be at least :length characters and contain at least one special character and one number.', [
                    'length' => $this->length,
                ]);

            default:
                return __('The :attribute must be at least :length characters.', [
                    'length' => $this->length,
                ]);
        }
    }

    /**
     * length sets the minimum length of the password.
     *
     * @param  int  $length
     * @return $this
     */
    public function length(int $length)
    {
        $this->length = $length;

        return $this;
    }

    /**
     * requireUppercase indicate that at least one uppercase character is required.
     * @return $this
     */
    public function requireUppercase()
    {
        $this->requireUppercase = true;

        return $this;
    }

    /**
     * requireNumeric indicate that at least one numeric digit is required.
     * @return $this
     */
    public function requireNumeric()
    {
        $this->requireNumeric = true;

        return $this;
    }

    /**
     * requireSpecialCharacter indicate that at least one special character is required.
     * @return $this
     */
    public function requireSpecialCharacter()
    {
        $this->requireSpecialCharacter = true;

        return $this;
    }

    /**
     * withMessage set the message that should be used when the rule fails.
     * @param  string  $message
     * @return $this
     */
    public function withMessage(string $message)
    {
        $this->message = $message;

        return $this;
    }
}
