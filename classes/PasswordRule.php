<?php namespace RainLab\User\Classes;

use Illuminate\Validation\Rules\Password as PasswordRuleBase;

/**
 * PasswordRule is a wrapper class
 */
class PasswordRule extends PasswordRuleBase
{
    /**
     * length sets the minimum length of the password.
     *
     * @param  int  $length
     * @return $this
     */
    public function length(int $length)
    {
        $this->min = $length;

        return $this;
    }
}
