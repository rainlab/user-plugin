<?php namespace RainLab\User\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * Timelines Backend Controller
 */
class Timelines extends Controller
{
    public $implement = [
        \Backend\Behaviors\ListController::class,
    ];

    /**
     * @var string listConfig file
     */
    public $listConfig = 'config_list.yaml';

    /**
     * @var array required permissions
     */
    public $requiredPermissions = ['rainlab.user.timelines'];

    /**
     * __construct the controller
     */
    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('RainLab.User', 'user', 'timelines');
    }

    /**
     * onRefreshList
     */
    public function onRefreshList()
    {
        return $this->listRefresh();
    }
}
