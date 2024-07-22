<?php namespace RainLab\User\Models\User;

use RainLab\User\Helpers\User as UserHelper;

/**
 * HasModelAttributes
 *
 * @property string $login
 * @property string $full_name
 * @property string $avatar_url
 * @property bool $is_banned
 * @property bool $is_activated
 * @property bool $is_two_factor_enabled
 *
 * @package rainlab\user
 * @author Alexey Bobkov, Samuel Georges
 */
trait HasModelAttributes
{
    /**
     * getLoginAttribute
     */
    public function getLoginAttribute()
    {
        $attribute = UserHelper::username();

        return $this->{$attribute};
    }

    /**
     * getFullNameAttribute
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * getAvatarUrl
     */
    public function getAvatarUrlAttribute()
    {
        return $this->relationLoaded('avatar')
            ? $this->getAvatarThumb()
            : null;
    }

    /**
     * getIsBannedAttribute
     */
    public function getIsBannedAttribute()
    {
        return $this->banned_at !== null;
    }

    /**
     * getIsActivatedAttribute
     */
    public function getIsActivatedAttribute()
    {
        return $this->activated_at !== null;
    }

    /**
     * getIsTwoFactorEnabledAttribute
     */
    public function getIsTwoFactorEnabledAttribute()
    {
        return $this->two_factor_confirmed_at !== null;
    }

    /**
     * setIsTwoFactorEnabledAttribute
     */
    public function setIsTwoFactorEnabledAttribute($value)
    {
        if ($value && !$this->two_factor_confirmed_at) {
            $this->two_factor_confirmed_at = $this->freshTimestamp();
        }

        if (!$value) {
            $this->two_factor_confirmed_at = null;
        }
    }

    /**
     * setPasswordAttribute protects the password from being reset to null
     */
    public function setPasswordAttribute($value)
    {
        if ($this->exists && empty($value)) {
            unset($this->attributes['password']);
        }
        else {
            $this->attributes['password'] = $value;
        }
    }

    /**
     * @deprecated use `login` attribute
     * @see getLoginAttribute
     */
    public function getLogin()
    {
        return $this->login;
    }
}
