<?php namespace RainLab\User\Models;

use Form;
use Model;

/**
 * Mail Blocker
 * 
 * A utility model that allows a user to block specific
 * mail views/templates from being sent to their address.
 */
class MailBlocker extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'rainlab_user_mail_blockers';

    /**
     * @var array Guarded fields
     */
    protected $guarded = [];

    /**
     * @var array Relations
     */
    public $belongsTo = [
        'user' => ['RainLab\User\Models\User']
    ];

    /**
     * Adds a block for a user and a mail view/template code.
     * @param string                   $template
     * @param RainLab\User\Models\User $user
     * @return bool
     */
    public static function addBlock($template, $user)
    {
        $blocker = static::firstOrNew([
            'template' => $template,
            'user_id' => $user->id
        ]);

        $blocker->email = $user->email;
        $blocker->save();
        return $blocker;
    }

    /**
     * Removes a block for a user and a mail view/template code.
     * @param string                   $template
     * @param RainLab\User\Models\User $user
     * @return bool
     */
    public static function removeBlock($template, $user)
    {
        $blocker = static::where([
            'template' => $template,
            'user_id' => $user->id
        ])->first();

        if (!$blocker)
            return false;

        $blocker->delete();
        return true;
    }

    /**
     * Toggles a set of templates on or off for a user, optionally filtered by a supplied set.
     * @param  array $templates   An array containing the template name as the key and the on/off value. If false, template is blocked. 
     * @param  RainLab\User\Models\User $user
     * @param  array $inTemplates An optional array where values are template names to process, others are ignored.
     * @return void
     */
    public static function toggleBlocks($templates, $user, array $inTemplates = null)
    {
        foreach ((array) $templates as $template => $value) {

            if ($inTemplates && !array_key_exists($template, $inTemplates) && !in_array($template, $inTemplates))
                continue;

            // Template uses an alias
            if (isset($inTemplates[$template]))
                $template = $inTemplates[$template];

            if ($value)
                static::removeBlock($template, $user);
            else
                static::addBlock($template, $user);
        }
    }

    /**
     * Updates mail blockers for a user if they change their email address
     * @param  Model $user
     * @return mixed
     */
    public static function syncUser($user)
    {
        return static::where('user_id', $user->id)->update(['email' => $user->email]);
    }

    /**
     * Returns a list of mail templates blocked by the user.
     * @param  Model $user
     * @return array
     */
    public static function checkAllForUser($user)
    {
        return static::where('user_id', $user->id)->lists('template');
    }

    /**
     * Checks if an email address has blocked a given template,
     * returns an array of blocked emails.
     * @param  string $template
     * @param  string $email
     * @return array
     */
    public static function checkForEmail($template, $email)
    {
        if (empty($email))
            return [];

        if (!is_array($email))
            $email = [$email => null];

        $emails = array_keys($email);

        return static::where('template', $template)
            ->whereIn('email', $emails)
            ->lists('email');
    }

    /**
     * Filters a Illuminate\Mail\Message and removes blocked recipients.
     * If no recipients remain, false is returned. Returns true if mailing
     * should proceed.
     * @param  string $template
     * @param  Illuminate\Mail\Message $message 
     * @return bool
     */
    public static function filterMessage($template, $message)
    {
        $recipients = $message->getTo();
        $blockedAddresses = static::checkForEmail($template, $recipients);
        if (!count($blockedAddresses))
            return true;

        foreach ($recipients as $address => $name) {
            if (in_array($address, $blockedAddresses))
                unset($recipients[$address]);
        }

        $message->setTo($recipients);
        return count($recipients) ? true : false;
    }

}