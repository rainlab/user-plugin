<?php namespace RainLab\User\Models;

use Model;

/**
 * UserGroup Model
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string $description
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon $created_at
 *
 * @package rainlab\user
 * @author Alexey Bobkov, Samuel Georges
 */
class UserGroup extends Model
{
    use \October\Rain\Database\Traits\Validation;

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
        'users' => [
            User::class,
            'table' => 'users_groups'
        ],
        'users_count' => [
            User::class,
            'table' => 'users_groups',
            'count' => true
        ]
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

    /**
     * scopeWithoutGuest
     */
    public function scopeWithoutGuest($query)
    {
        return $query->where('code', '<>', self::GROUP_GUEST);
    }
}
