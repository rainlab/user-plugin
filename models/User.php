<?php namespace RainLab\User\Models;

use URL;
use Mail;
use October\Rain\Auth\Models\User as UserBase;
use RainLab\User\Models\Settings as UserSettings;

class User extends UserBase
{
    /**
     * @var string The database table used by the model.
     */
    protected $table = 'users';

    /**
     * Validation rules
     */
    public $rules = [
        'email' => 'required|between:3,64|email|unique:users',
        'username' => 'required|between:2,64|unique:users',
        'password' => 'required:create|between:4,64|confirmed',
        'password_confirmation' => 'required_with:password|between:4,64'
    ];

    /**
     * @var array Relations
     */
    public $belongsToMany = [
        // 'groups' => ['RainLab\User\Models\Group', 'table' => 'users_groups']
    ];

    public $belongsTo = [
        'country' => ['RainLab\User\Models\Country'],
        'state'   => ['RainLab\User\Models\State'],
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
        'email',
        'password',
        'password_confirmation',
        'company',
        'phone',
        'street_addr',
        'city',
        'zip',
        'country',
        'state'
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
        if (static::$loginAttribute !== null)
            return static::$loginAttribute;

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

    public function getCountryOptions()
    {
        return Country::getNameList();
    }

    public function getStateOptions()
    {
        return State::getNameList($this->country_id);
    }

    /**
     * Gets a code for when the user is persisted to a cookie or session which identifies the user.
     * @return string
     */
    public function getPersistCode()
    {
        if (!$this->persist_code)
            return parent::getPersistCode();

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
            return '//www.gravatar.com/avatar/' .
                md5(strtolower(trim($this->email))) .
                '?s='. $size .
                '&d='. urlencode($default);
        }
    }

    /**
     * Sends the confirmation email to a user, after activating
     * @param  string $code
     * @return void
     */
    public function attemptActivation($code)
    {
        $result = parent::attemptActivation($code);
        if ($result === false) {
            return false;
        }

        if ($mailTemplate = UserSettings::get('welcome_template')) {
            $data = [
                'name' => $this->name,
                'email' => $this->email
            ];

            Mail::send($mailTemplate, $data, function($message) {
                $message->to($this->email, $this->name);
            });
        }

        return true;
    }

    /**
     * Looks up a user by their email address.
     * @return self
     */
    public static function findByEmail($email)
    {
        if (!$email) return;
        return self::where('email', $email)->first();
    }

}
