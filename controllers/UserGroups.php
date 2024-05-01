<?php namespace RainLab\User\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * UserGroups Controller
 */
class UserGroups extends Controller
{
    /**
     * @var array implement extensions
     */
    public $implement = [
        \Backend\Behaviors\FormController::class,
        \Backend\Behaviors\ListController::class
    ];

    /**
     * @var array formConfig configuration.
     */
    public $formConfig = 'config_form.yaml';

    /**
     * @var array listConfig configuration.
     */
    public $listConfig = 'config_list.yaml';

    /**
     * @var array relationConfig for extensions.
     */
    public $relationConfig;

    /**
     * @var array requiredPermissions to view this page.
     */
    public $requiredPermissions = ['rainlab.users.access_groups'];

    /**
     * __construct
     */
    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('RainLab.User', 'user', 'users');
    }
}
