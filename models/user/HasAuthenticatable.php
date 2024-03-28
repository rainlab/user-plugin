<?php namespace RainLab\User\Models\User;

/**
 * HasAuthenticatable contract
 */
trait HasAuthenticatable
{
    /**
     * @var string rememberTokenName is the column name of the "remember me" token.
     */
    protected $rememberTokenName = 'remember_token';

    /**
     * getAuthIdentifierName of the unique identifier for the user.
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return $this->getKeyName();
    }

    /**
     * getAuthIdentifier for the user.
     */
    public function getAuthIdentifier()
    {
        return $this->{$this->getAuthIdentifierName()};
    }

    /**
     * getAuthIdentifierForBroadcasting
     */
    public function getAuthIdentifierForBroadcasting()
    {
        return $this->getAuthIdentifier();
    }

    /**
     * getAuthPassword for the user.
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * getRememberToken value for the "remember me" session.
     * @return string|null
     */
    public function getRememberToken()
    {
        if (!empty($this->getRememberTokenName())) {
            return (string) $this->{$this->getRememberTokenName()};
        }
    }

    /**
     * setRememberToken value for the "remember me" session.
     * @param  string  $value
     */
    public function setRememberToken($value)
    {
        if (!empty($this->getRememberTokenName())) {
            $this->{$this->getRememberTokenName()} = $value;
        }
    }

    /**
     * getRememberTokenName gets the column name for the "remember me" token.
     * @return string
     */
    public function getRememberTokenName()
    {
        return $this->rememberTokenName;
    }
}
