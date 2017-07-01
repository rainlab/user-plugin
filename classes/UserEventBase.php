<?php namespace RainLab\User\Classes;

use RainLab\Notify\Classes\EventBase;

class UserEventBase extends EventBase
{
    /**
     * Defines the usable parameters provided by this class.
     */
    public function defineParams()
    {
        return [
            'name' => [
                'title' => 'Name',
                'label' => 'Name of the user',
            ],
            'email' => [
                'title' => 'Email',
                'label' => "User's email address",
            ],
        ];
    }

    /**
     * Local conditions supported by this event.
     */
    public function defineConditions()
    {
        return [
            \RainLab\User\NotifyRules\UserAttributeCondition::class
        ];
    }

    public static function makeParamsFromEvent(array $args, $eventName = null)
    {
        $user = array_get($args, 0);

        $params = $user->getNotificationVars();
        $params['user'] = $user;

        return $params;
    }
}
