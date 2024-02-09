<?php namespace RainLab\User\Models\User;

/**
 * HasModelAttributes
 *
 * @property bool $is_banned
 * @property bool $is_activated
 *
 * @package rainlab\user
 * @author Alexey Bobkov, Samuel Georges
 */
trait HasModelAttributes
{
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
}

