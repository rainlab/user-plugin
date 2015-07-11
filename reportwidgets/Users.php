<?php namespace Mja\Extensions\ReportWidgets;

use Carbon\Carbon;
use Backend\Classes\ReportWidgetBase;
use RainLab\User\Models\User;

/**
 * Users Form Widget
 */
class Users extends ReportWidgetBase
{

   /**
     * Renders the widget.
     */
    public function render()
    {
        $this->vars['registrations'] = [];
        $this->vars['logins'] = [];
        $date = Carbon::parse('tomorrow');

        for ($i = 0; $i <= $this->property('days'); $i++) {
            $yesterday = clone $date;
            $yesterday->subDay(1);

            $this->vars['registrations'][] = [
                $yesterday->getTimestamp() * 1000,
                User::whereBetween('created_at', [
                    $yesterday,
                    $date,
                ])->remember(60)->count()
            ];

            $this->vars['logins'][] = [
                $yesterday->getTimestamp() * 1000,
                User::whereBetween('last_login', [
                    $yesterday, $date,
                ])->remember(60)->count()
            ];

            $date = $yesterday;
        }

        return $this->makePartial('users');
    }

    public function defineProperties()
    {
        return [
            'title' => [
                'title'             => 'Widget title',
                'default'           => 'Users',
                'type'              => 'string',
                'validationPattern' => '^.+$',
                'validationMessage' => 'The Widget Title is required.'
            ],
            'days' => [
                'title'             => 'Number of days to display data for',
                'default'           => '30',
                'type'              => 'string',
                'validationPattern' => '^[0-9]+$'
            ]
        ];
    }

}
