<?php namespace RainLab\User\Models;

use October\Rain\Auth\Models\Group as GroupBase;

/**
 * UserGroup Model
 */
class UserGroup extends GroupBase
{
    const GROUP_GUEST = 'guest';
    const GROUP_REGISTERED = 'registered';

    /**
     * @var string The database table used by the model.
     */
    protected $table = 'user_groups';

    /**
     * Validation rules
     */
    public $rules = [
        'name' => 'required|between:3,64',
        'code' => 'required|regex:/^[a-zA-Z0-9_\-]+$/|unique:user_groups',
    ];

    /**
     * @var array Relations
     */
    public $belongsToMany = [
        'users' => [User::class, 'table' => 'users_groups'],
        'users_count' => [User::class, 'table' => 'users_groups', 'count' => true]
    ];

    /**
     * @var array The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'code',
        'description'
    ];

    /**
     * @var object|null guestGroupCache
     */
    protected static $guestGroupCache = null;

    /**
     * getGuestGroup returns the guest user group.
     */
    public static function getGuestGroup(): ?static
    {
        if (self::$guestGroupCache !== null) {
            return self::$guestGroupCache;
        }

        $group = self::where('code', self::GROUP_GUEST)->first();

        return self::$guestGroupCache = $group;
    }
}
