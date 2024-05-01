<?php namespace RainLab\User\Models;

use Db;
use Str;
use Date;
use October\Rain\Database\Model;

/**
 * UserPreference Model
 *
 * @property int $id
 * @property int $user_id
 * @property string $item
 * @property array $value
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon $created_at
 *
 * @package rainlab\user
 * @author Alexey Bobkov, Samuel Georges
 */
class UserPreference extends Model
{
    /**
     * @var string The database table used by the model.
     */
    public $table = 'rainlab_user_preferences';

    /**
     * @var array jsonable attribute names that are json encoded and decoded from the database
     */
    protected $jsonable = ['value'];

    /**
     * @var array Relations
     */
    public $belongsTo = [
        'user' => User::class
    ];

    /**
     * @var array preferenceCache
     */
    protected static $preferenceCache = [];

    /**
     * setPreferences for a user. Eg:
     *
     *     UserPreference::setPreferences($user, ['send_promotional_emails' => 0, ...]);
     *
     * @param  string $userId
     * @param  array $preferences
     * @return void
     */
    public static function setPreferences($userId, $preferences)
    {
        foreach ($preferences as $item => $value) {
            static::setPreference($userId, $item, $value);
        }
    }

    /**
     * setPreferencesSafe fills preferences with user input. Only basic values are allowed
     * like A-Z ascii and boolean switches.
     */
    public static function setPreferencesSafe($userId, $preferences)
    {
        array_walk_recursive($preferences, function(&$value, $key) {
            if ($value === 'null') {
                $value = null;
            }
            elseif ($value === 'true') {
                $value = true;
            }
            elseif ($value === 'false') {
                $value = false;
            }
            elseif (is_bool($value) || is_numeric($value)) {
                $value = $value;
            }
            else {
                $value = Str::slug($value, ' ');
            }
        });

        static::setPreferences($userId, $preferences);
    }

    /**
     * setPreference sets a single preference for a user
     */
    public static function setPreference($userId, $item, $value)
    {
        if (!$userId) {
            return;
        }

        unset(static::$preferenceCache[$userId]);

        if ($value === null) {
            static::where('user_id', $userId)->where('item', $item)->delete();
            return;
        }

        Db::table('rainlab_user_preferences')->updateOrInsert([
            'user_id' => $userId,
            'item' => $item,
        ], [
            'value' => json_encode($value, JSON_UNESCAPED_UNICODE),
            'created_at' => Date::now(),
            'updated_at' => Date::now()
        ]);
    }

    /**
     * getPreference
     */
    public static function getPreference($userId, $item, $default = null)
    {
        if (!$userId) {
            return $default;
        }

        $cache = static::$preferenceCache[$userId] ?? null;
        if ($cache === null) {
            $cache = Db::table('rainlab_user_preferences')
                ->where('user_id', $userId)
                ->pluck('value', 'item')
                ->all()
            ;
        }

        static::$preferenceCache[$userId] = $cache;

        if (!isset($cache[$item])) {
            return $default;
        }

        return json_decode($cache[$item], true);
    }

    /**
     * resetAll preferences for a user.
     * @param RainLab\User\Models\User $user
     * @return void
     */
    public static function resetAll($userId)
    {
        if ($userId) {
            static::where('user_id', $userId)->delete();
            unset(static::$preferenceCache[$userId]);
        }
    }

    /**
     * filterMailMessage filters a Illuminate\Mail\Message and removes blocked recipients.
     * If no recipients remain, false is returned. Returns null if mailing
     * should proceed.
     * @param  string $template
     * @param  Illuminate\Mail\Message $message
     * @return bool|null
     */
    public static function filterMailMessage($template, $message)
    {
        $safeTemplates = [
            'user:invite_email',
            'user:recover_password',
            'user:verify_email',
        ];

        if (in_array($template, $safeTemplates)) {
            return null;
        }

        $recipients = $message->getTo();
        if (empty($recipients)) {
            return null;
        }

        $emails = array_keys(is_array($recipients) ? $recipients : [$recipients => null]);

        $blockedEmails = User::where('is_mail_blocked', true)
            ->whereIn('email', $emails)
            ->pluck('email')
            ->all()
        ;

        if (!count($blockedEmails)) {
            return null;
        }

        foreach ($recipients as $index => $address) {
            if (in_array($address->getAddress(), $blockedEmails)) {
                unset($recipients[$index]);
            }
        }

        // Override recipients
        $message->to($recipients, null, true);

        return count($recipients) ? null : false;
    }
}
