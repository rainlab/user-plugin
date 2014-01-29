<?php namespace RainLab\User\Controllers;

use Flash;
use BackendMenu;
use BackendAuth;
use Backend\Classes\BackendController;

class Users extends BackendController
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public $requiredPermissions = ['october.manage_users'];

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('RainLab.User', 'user', 'users');
    }

    /**
     * Manually activate a user
     */
    public function update_onActivate($recordId = null)
    {
        $model = $this->formFindModelObject($recordId);

        $model->attemptActivation($model->activation_code);

        Flash::success('User has been activated successfully!');

        if ($redirect = $this->makeRedirect('update', $model))
            return $redirect;
    }
}