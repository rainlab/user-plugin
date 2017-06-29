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
     * @var array Notification vars
     */
    public $vars;

    /**
     * Defines the properties used by this class.
     */
    public function defineProperties()
    {
        return [
            'name' => [],
            'email' => [],
            'username' => [],
            'login' => [],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function defineFormFields()
    {
        return 'fields.yaml';
    }
}
