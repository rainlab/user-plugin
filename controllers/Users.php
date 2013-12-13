<?php namespace Plugins\October\User\Controllers;

use BackendMenu;
use BackendAuth;
use Modules\Backend\Classes\BackendController;

class Users extends BackendController
{
    public $implement = [
        'Modules.Backend.Behaviors.FormController',
        'Modules.Backend.Behaviors.ListController'
    ];

    public $formConfig = 'form_config.yaml';
    public $listConfig = 'list_config.yaml';

    public $requiredPermissions = ['october.manage_users'];

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('October.User', 'user', 'users');
    }
}