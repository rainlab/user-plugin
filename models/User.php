<?php namespace RainLab\User\Models;

use Str;
use Mail;
use Event;
use Model;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword;
use October\Rain\Auth\AuthException;

/**
 * User record
 *
 * @property int $id
 * @property bool $is_guest
 * @property string $first_name
 * @property string $last_name
 * @property string $username
 * @property string $email
 * @property string $company
 * @property string $phone
 * @property string $city
 * @property string $zip
 * @property int $state_id
 * @property int $country_id
 * @property string $notes
 * @property string $password
 * @property string $remember_token
 * @property string $two_factor_secret
 * @property string $two_factor_recovery_codes
 * @property int $primary_group_id
 * @property string $created_ip_address
 * @property string $last_ip_address
 * @property string $banned_reason
 * @property \Illuminate\Support\Carbon $banned_at
 * @property \Illuminate\Support\Carbon $activated_at
 * @property \Illuminate\Support\Carbon $two_factor_confirmed_at
 * @property \Illuminate\Support\Carbon $deleted_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon $created_at
 *
 * @package rainlab\user
 * @author Alexey Bobkov, Samuel Georges
 */
class User extends Model implements Authenticatable, CanResetPassword
{
    use \RainLab\User\Models\User\HasTwoFactor;
    use \RainLab\User\Models\User\HasPersistCode;
    use \RainLab\User\Models\User\HasPasswordReset;
    use \RainLab\User\Models\User\HasAuthenticatable;
    use \RainLab\User\Models\User\HasEmailVerification;
    use \RainLab\User\Models\User\HasModelAttributes;
    use \RainLab\User\Models\User\HasModelScopes;
    use \October\Rain\Database\Traits\SoftDelete;
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\Encryptable;
    use \October\Rain\Database\Traits\Purgeable;
    use \October\Rain\Database\Traits\Hashable;

    /**
     * @var string table associated with the model
     */
    protected $table = 'users';

    /**
     * @var array rules
     */
    public $rules = [
        'first_name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'between:3,255', 'email', 'unique:users,email,NULL,id,is_guest,false'],
        'username' => ['required', 'between:2,255', 'unique:users,username,NULL,id,is_guest,false'],
        'password' => ['required:create', 'string', 'confirmed'],
        'avatar' => ['nullable', 'image', 'max:4000'],
    ];

    /**
     * @var array dates
     */
    protected $dates = [
        'last_seen',
        'last_login',
        'banned_at',
        'deleted_at',
        'created_at',
        'updated_at',
        'activated_at',
        'two_factor_confirmed_at',
    ];

    /**
     * @var array fillable attributes
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'login',
        'username',
        'email',
        'password',
        'password_confirmation',
    ];

    /**
     * hidden attributes
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret'
    ];

    /**
     * @var array purgeable attribute names which should not be saved to the database.
     */
    protected $purgeable = [
        'password_confirmation',
        'send_invite'
    ];

    /**
     * @var array hashable list of attribute names which should be hashed using the Bcrypt hashing algorithm
     */
    protected $hashable = [
        'password'
    ];

    /**
     * @var array encryptable is a list of attribute names which should be encrypted
     */
    protected $encryptable = ['two_factor_secret', 'two_factor_recovery_codes'];

    /**
     * appends accessors to the model's array form.
     */
    protected $appends = [
        'avatar_url'
    ];

    /**
     * @var array belongsToMany
     */
    public $belongsToMany = [
        'groups' => [
            UserGroup::class,
            'table' => 'users_groups'
        ]
    ];

    /**
     * @var array belongsTo
     */
    public $belongsTo = [
        'primary_group' => UserGroup::class
    ];

    /**
     * @var array attachOne
     */
    public $attachOne = [
        'avatar' => \System\Models\File::class
    ];

    /**
     * @var string|null loginAttribute
     */
    public static $loginAttribute = null;

    /**
     * Converts a guest user to a registered one and sends an invitation notification.
     * @return void
     */
    public function convertToRegistered($sendNotification = true)
    {
        // Already a registered user
        if (!$this->is_guest) {
            return;
        }

        if ($sendNotification) {
            $this->generatePassword();
        }

        $this->is_guest = false;
        $this->save();

        if ($sendNotification) {
            $this->sendInvitation();
        }
    }

    /**
     * Returns the public image file path to this user's avatar.
     */
    public function getAvatarThumb($size = 64, $options = null)
    {
        if (is_string($options)) {
            $options = ['default' => $options];
        }
        elseif (!is_array($options)) {
            $options = [];
        }

        // Default is "mm" (Mystery man)
        $default = array_get($options, 'default', 'mm');

        if ($this->avatar) {
            return $this->avatar->getThumb($size, $size, $options);
        }
        else {
            return '//www.gravatar.com/avatar/'.
                md5(strtolower(trim($this->email))).
                '?s='.$size.
                '&d='.urlencode($default);
        }
    }

    //
    // Events
    //

    /**
     * smartDelete will only delete a user if the user.canDeleteUser event
     * allows it to happen.
     */
    public function smartDelete()
    {
        /**
         * @event user.canDeleteUser
         * Triggered before a user is deleted. This event should return true if the
         * user has dependencies and should be soft deleted to retain those relationships
         * and allow the user to be restored. Otherwise, it will be deleted forever.
         *
         * Example usage:
         *
         *     Event::listen('user.canDeleteUser', function($user) {
         *         if ($user->orders->count()) {
         *             return true;
         *         }
         *     });
         *
         */
        if (Event::fire('user.canDeleteUser', [$this], true) === false) {
            return $this->delete();
        }

        return $this->forceDelete();
    }

    /**
     * beforeValidate event
     * @return void
     */
    public function beforeValidate()
    {
        // Guests are special
        if ($this->is_guest) {
            $this->removeValidationRule('email', 'unique');
        }

        if ($this->is_guest && !$this->password) {
            $this->generatePassword();
        }

        // Confirmation would be an empty string if provided by a form
        if ($this->password && $this->password_confirmation === null) {
            $this->password_confirmation = $this->password;
        }

        // When the username is not used, the email is substituted.
        if (!$this->username || ($this->isDirty('email') && $this->getOriginal('email') == $this->username)) {
            $this->username = $this->email;
        }

        // Apply rules Setting
        $this->addValidationRule('password', Setting::makePasswordRule());
    }

    /**
     * afterCreate event
     * @return void
     */
    public function afterCreate()
    {
        $this->restorePurgedValues();

        if ($this->send_invite) {
            $this->sendInvitation();
        }
    }

    /**
     * beforeLogin event
     * @return void
     */
    public function beforeLogin()
    {
        if ($this->is_guest) {
            $login = $this->login;
            throw new AuthException(sprintf(
                'Cannot login user "%s" as they are not registered.', $login
            ));
        }

        parent::beforeLogin();
    }

    /**
     * afterLogin event
     * @return void
     */
    public function afterLogin()
    {
        $this->last_login = $this->freshTimestamp();

        if ($this->trashed()) {
            $this->restore();

            Mail::sendTo($this, 'rainlab.user::mail.reactivate', [
                'name' => $this->first_name
            ]);

            Event::fire('rainlab.user.reactivate', [$this]);
        }
        else {
            parent::afterLogin();
        }

        Event::fire('rainlab.user.login', [$this]);
    }

    /**
     * afterDelete event
     * @return void
     */
    public function afterDelete()
    {
        if ($this->isSoftDelete()) {
            Event::fire('rainlab.user.deactivate', [$this]);
            return;
        }

        $this->avatar && $this->avatar->delete();

        parent::afterDelete();
    }

    //
    // Banning
    //

    /**
     * ban this user, preventing them from signing in.
     */
    public function ban($reason = null)
    {
        if (!$this->is_banned) {
            $this->banned_reason = $reason;
            $this->banned_at = $this->freshTimestamp();
            $this->save(['force' => true]);
        }
    }

    /**
     * unban removes the ban on this user.
     */
    public function unban()
    {
        if ($this->is_banned) {
            $this->banned_reason = null;
            $this->banned_at = null;
            $this->save(['force' => true]);
        }
    }

    //
    // Last Seen
    //

    /**
     * touchIpAddress records the last_ip_address to reflect the last known IP for this user.
     */
    public function touchIpAddress(?string $ipAddress)
    {
        $this
            ->newQuery()
            ->where('id', $this->id)
            ->update(['last_ip_address' => $ipAddress])
        ;
    }

    /**
     * touchLastSeen checks if the user has been seen in the last 5 minutes, and if not,
     * updates the last_seen timestamp to reflect their online status.
     */
    public function touchLastSeen()
    {
        if ($this->isOnline()) {
            return;
        }

        $oldTimestamps = $this->timestamps;
        $this->timestamps = false;

        $this
            ->newQuery()
            ->where('id', $this->id)
            ->update(['last_seen' => $this->freshTimestamp()])
        ;

        $this->last_seen = $this->freshTimestamp();
        $this->timestamps = $oldTimestamps;
    }

    /**
     * isOnline returns true if the user has been active within the last 5 minutes.
     */
    public function isOnline(): bool
    {
        if (!$this->last_seen) {
            return false;
        }

        return $this->last_seen > $this->freshTimestamp()->subMinutes(5);
    }

    /**
     * getLastSeen returns the date this user was last seen.
     * @deprecated use last_seen attribute
     * @return Carbon\Carbon
     */
    public function getLastSeen()
    {
        return $this->last_seen ?: $this->created_at;
    }

    //
    // Utils
    //

    /**
     * getNotificationVars returns the variables available when sending a user notification.
     */
    public function getNotificationVars(): array
    {
        $vars = [
            'full_name' => $this->full_name,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'login' => $this->login,
            'email' => $this->email,
            'username' => $this->username,
        ];

        // Extensibility
        $result = Event::fire('user.getNotificationVars', [$this]);
        if ($result && is_array($result)) {
            $vars = call_user_func_array('array_merge', $result) + $vars;
        }

        return $vars;
    }

    /**
     * sendInvitation sends an invitation to the user using template
     * "rainlab.user::mail.invite"
     */
    protected function sendInvitation()
    {
        Mail::sendTo($this, 'rainlab.user::mail.invite', $this->getNotificationVars());
    }

    /**
     * generatePassword assigns this user with a random password.
     */
    public function generatePassword()
    {
        $this->password = $this->password_confirmation = Str::random(12);
    }
}
