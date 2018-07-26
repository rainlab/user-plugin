<?php namespace RainLab\User\ReportWidgets;

use Backend\Classes\ReportWidgetBase;
use Carbon\Carbon;
use Exception;
use RainLab\User\Models\User;

class NewUsers extends ReportWidgetBase
{
    /**
     * Renders the widget.
     */
    public function render()
    {
        try {
            $this->loadData();
        } catch (Exception $e) {
            $this->vars['error'] = $e->getMessage();
        }

        return $this->makePartial('widget');
    }

    /**
     * Define widget properties.
     * 
     * @return array
     */
    public function defineProperties()
    {
        return [
            'title' => [
                'default'           => e(trans('rainlab.user::lang.widgets.new_users_title')),
                'title'             => 'backend::lang.dashboard.widget_title_label',
                'type'              => 'string',
                'validationMessage' => 'backend::lang.dashboard.widget_title_error',
                'validationPattern' => '^.+$',
            ],
            'show_day' => [
                'default'   => true,
                'title'     => 'rainlab.user::lang.widgets.new_users_day_property',
                'type'      => 'checkbox',
            ],
            'show_week' => [
                'default'   => false,
                'title'     => 'rainlab.user::lang.widgets.new_users_week_property',
                'type'      => 'checkbox',
            ],
            'show_month' => [
                'default'   => false,
                'title'     => 'rainlab.user::lang.widgets.new_users_month_property',
                'type'      => 'checkbox',
            ],
            'show_year' => [
                'default'   => false,
                'title'     => 'rainlab.user::lang.widgets.new_users_year_property',
                'type'      => 'checkbox',
            ],
        ];
    }

    /**
     * Load widget data.
     */
    protected function loadData()
    {
        $query = function($start, $previous) {
            return [
                'current' => User::where('created_at', '>=', $start)
                    ->count(),
                'previous' => User::where('created_at', '>=', $previous)
                    ->where('created_at', '<', $start)
                    ->count(),
            ];
        };

        if ($this->property('show_day')) {
            $this->vars['day'] = $query(
                Carbon::now()->startOfDay(),
                Carbon::yesterday()->startOfDay()
            );
        }

        if ($this->property('show_week')) {
            $this->vars['week'] = $query(
                Carbon::now()->startOfWeek(),
                Carbon::now()->subWeek()->startOfWeek()
            );
        }

        if ($this->property('show_month')) {
            $this->vars['month'] = $query(
                Carbon::now()->startOfMonth(),
                Carbon::now()->subMonth()->startOfMonth()
            );
        }

        if ($this->property('show_year')) {
            $this->vars['year'] = $query(
                Carbon::now()->startOfYear(),
                Carbon::now()->subYear()->startOfYear()
            );
        }
    }
}