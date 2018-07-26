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
            'widget_title' => [
                'default'           => e(trans('rainlab.user::lang.widgets.new_users.widget_title')),
                'title'             => 'backend::lang.dashboard.widget_title_label',
                'type'              => 'string',
                'validationMessage' => 'backend::lang.dashboard.widget_title_error',
                'validationPattern' => '^.+$',
            ],
            'show_today' => [
                'default'   => true,
                'title'     => 'rainlab.user::lang.widgets.new_users.show_today',
                'type'      => 'checkbox',
            ],
            'show_week' => [
                'default'   => false,
                'title'     => 'rainlab.user::lang.widgets.new_users.show_week',
                'type'      => 'checkbox',
            ],
            'show_month' => [
                'default'   => false,
                'title'     => 'rainlab.user::lang.widgets.new_users.show_month',
                'type'      => 'checkbox',
            ],
            'show_year' => [
                'default'   => false,
                'title'     => 'rainlab.user::lang.widgets.new_users.show_year',
                'type'      => 'checkbox',
            ],
        ];
    }

    protected function loadAssets()
    {
        $this->addCss('css/style.css', 'RainLab.User');
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

        if ($this->property('show_today')) {
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