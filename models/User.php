<?php namespace RainLab\User\Models;

use Mail;
use Event;
use October\Rain\Auth\Models\User as UserBase;
use RainLab\User\Models\Settings as UserSettings;

class User extends UserBase
{
    use \October\Rain\Database\Traits\SoftDeleting;

    /**
     * @var string The database table used by the model.
     */
    protected $table = 'users';

    /**
     * Validation rules
     */
    public $rules = [
        'email'    => 'required|between:6,255|email|unique:users',
        'username' => 'required|between:2,255|unique:users',
        'password' => 'required:create|between:4,255|confirmed',
        'password_confirmation' => 'required_with:password|between:4,255'
    ];

    /**
     * @var array Relations
     */
    public $belongsToMany = [
        'groups' => ['RainLab\User\Models\UserGroup', 'table' => 'users_groups']
    ];

    public $attachOne = [
        'avatar' => ['System\Models\File']
    ];

    /**
     * @var array The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'surname',
        'login',
        'username',
        'email',
        'password',
        'password_confirmation'
    ];

    /**
     * Purge attributes from data set.
     */
    protected $purgeable = ['password_confirmation'];

    public static $loginAttribute = null;

    /**
     * @return string Returns the name for the user's login.
     */
    public function getLoginName()
    {
        if (static::$loginAttribute !== null) {
            return static::$loginAttribute;
        }

        return static::$loginAttribute = UserSettings::get('login_attribute', UserSettings::LOGIN_EMAIL);
    }

    /**
     * Before validation event
     * @return void
     */
    public function beforeValidate()
    {
        /*
         * When the username is not used, the email is substituted.
         */
        if (
            (!$this->username) ||
            ($this->isDirty('email') && $this->getOriginal('email') == $this->username)
        ) {
            $this->username = $this->email;
        }
    }

    public function afterLogin()
    {
        if ($this->trashed()) {
            $this->last_login = $this->freshTimestamp();
            $this->restore();

            Mail::sendTo($this, 'rainlab.user::mail.reactivate', [
                'name' => $this->name
            ]);

            Event::fire('rainlab.user.reactivate', [$this]);
        }
        else {
            parent::afterLogin();
        }

        Event::fire('rainlab.user.login', [$this]);
    }

    /**
     * After delete event
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

    public function scopeIsActivated($query)
    {
        return $query->where('is_activated', 1);
    }

    public function scopeFilterByGroup($query, $filter)
    {
        return $query->whereHas('groups', function($group) use ($filter) {
            $group->whereIn('id', $filter);
        });
    }

    /**
     * Gets a code for when the user is persisted to a cookie or session which identifies the user.
     * @return string
     */
    public function getPersistCode()
    {
        if (!$this->persist_code) {
            return parent::getPersistCode();
        }

        return $this->persist_code;
    }

    /**
     * Returns the public image file path to this user's avatar.
     */
    public function getAvatarThumb($size = 25, $options = null)
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

    /**
     * Sends the confirmation email to a user, after activating.
     * @param string $code
     * @param array $templateParams
     * @return void
     */
    public function attemptActivation($code, $templateParams = [])
    {
        $result = parent::attemptActivation($code);
        if ($result === false) {
            return false;
        }

        // default template parameters
        $templateParams += [
            'name'  => $this->name,
            'email' => $this->email,
        ];

        if ($mailTemplate = UserSettings::get('welcome_template')) {
            Mail::sendTo($this, $mailTemplate, $templateParams);
        }

        return true;
    }

    /**
     * Looks up a user by their email address.
     * @return self
     */
    public static function findByEmail($email)
    {
        if (!$email) {
            return;
        }

        return self::where('email', $email)->first();
    }

    //
    // Last Seen
    //

    /**
     * Checks if the user has been seen in the last 5 minutes, and if not,
     * updates the last_login timestamp to reflect their online status.
     * @return void
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
            ->update(['last_login' => $this->freshTimestamp()])
        ;

        $this->timestamps = $oldTimestamps;
    }

    /**
     * Returns true if the user has been active within the last 5 minutes.
     * @return bool
     */
    public function isOnline()
    {
        return $this->getLastSeen() > $this->freshTimestamp()->subMinutes(5);
    }

    /**
     * Returns the date this user was last seen.
     * @return Carbon\Carbon
     */
    public function getLastSeen()
    {
        return $this->last_login ?: $this->created_at;
    }
}
