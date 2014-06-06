<?php namespace RainLab\User\Controllers;

use Flash;
use BackendMenu;
use BackendAuth;
use Backend\Classes\Controller;

class Users extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public $requiredPermissions = ['october.manage_users'];

    public $bodyClass = 'compact-container';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('RainLab.User', 'user', 'users');

        /* @todo Remove line if year >= 2015 */ if (\Schema::hasColumn('users', 'activated')) \Schema::table('users', function($table) { $table->renameColumn('activated', 'is_activated'); });
        /* @todo Remove line if year >= 2015 */ if (\Schema::hasColumn('user_throttle', 'suspended')) \Schema::table('user_throttle', function($table) { $table->renameColumn('suspended', 'is_suspended'); });
        /* @todo Remove line if year >= 2015 */ if (\Schema::hasColumn('user_throttle', 'banned')) \Schema::table('user_throttle', function($table) { $table->renameColumn('banned', 'is_banned'); });
        /* @todo Remove line if year >= 2015 */ if (\Schema::hasColumn('rainlab_user_countries', 'enabled')) \Schema::table('rainlab_user_countries', function($table) { $table->renameColumn('enabled', 'is_enabled'); });
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