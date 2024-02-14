<?php namespace RainLab\User\Models\User;

/**
 * HasModelAttributes
 *
 * @property string $full_name
 * @property string $avatar_url
 * @property bool $is_banned
 * @property bool $is_activated
 *
 * @package rainlab\user
 * @author Alexey Bobkov, Samuel Georges
 */
trait HasModelAttributes
{
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
    public function getAvatarUrl()
    {
        return $this->getAvatarThumb();
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
}
