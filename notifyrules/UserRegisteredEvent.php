<?php namespace RainLab\User\NotifyRules;

use RainLab\Notify\Classes\EventBase;

class UserRegisteredEvent extends EventBase
{
    /**
     * Returns information about this event, including name and description.
     */
    public function eventDetails()
    {
        return [
            'name'        => 'Registered',
            'description' => 'A user has registered',
            'group'       => 'user'
        ];
    }

    /**
     * Defines the properties used by this class.
     */
    public function defineParams()
    {
        return [
            'name' => [],
            'email' => [],
            'username' => [],
            'login' => [],
        ];
    }
}
