<?php namespace RainLab\User\Models;

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
        'login' => 'required|between:2,64|unique:users',
        'email' => 'required|between:3,64|email|unique:users',
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
    protected $fillable = ['name', 'email', 'password', 'password_confirmation', 'country', 'state'];

    /**
     * Purge attributes from data set.
     */
    protected $purgeable = ['password_confirmation'];

    protected static $loginAttribute = 'login';

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
    public function getAvatarThumb($size = 25, $default = null)
    {
        if ($this->avatar)
            return $this->avatar->getThumb($size, $size);
        else
            return '//www.gravatar.com/avatar/' . md5(strtolower(trim($this->email))) . '?s='.$size.'&d='.urlencode($default);
    }

    /**
     * Sends the confirmation email to a user, after activating
     * @param  string $code
     * @return void
     */
    public function attemptActivation($code)
    {
        $result = parent::attemptActivation($code);
        if ($result === false)
            return false;

        if (!$mailTemplate = UserSettings::get('welcome_template'))
            return;

        $data = [
            'name' => $this->name,
            'email' => $this->email
        ];

        Mail::send($mailTemplate, $data, function($message)
        {
            $message->to($this->email, $this->name);
        });
    }

}
