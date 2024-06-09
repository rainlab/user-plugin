<?php namespace RainLab\User\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use RainLab\User\Models\Setting as UserSetting;
use RainLab\User\Models\UserLog;
use RainLab\User\Helpers\User as UserHelper;

/**
 * Users controller
 */
class Users extends Controller
{
    use \RainLab\User\Controllers\Users\HasEditActions;
    use \RainLab\User\Controllers\Users\HasBulkActions;

    /**
     * @var array implement extensions
     */
    public $implement = [
        \Backend\Behaviors\FormController::class,
        \Backend\Behaviors\ListController::class,
        \Backend\Behaviors\RelationController::class,
        \Backend\Behaviors\ImportExportController::class
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
    public $relationConfig = 'config_relation.yaml';

    /**
     * @var array importExportConfig configuration.
     */
    public $importExportConfig = 'config_import_export.yaml';

    /**
     * @var array requiredPermissions to view this page.
     */
    public $requiredPermissions = ['rainlab.users.access_users'];

    /**
     * __construct
     */
    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('RainLab.User', 'user', 'users');
    }

    /**
     * {@inheritDoc}
     */
    public function listInjectRowClass($record, $definition = null)
    {
        $classes = [];

        if ($record->trashed()) {
            $classes[] = 'strike';
        }

        if ($record->is_banned) {
            $classes[] = 'negative';
        }

        if (!$record->is_activated) {
            $classes[] = 'disabled';
        }

        if (count($classes) > 0) {
            return join(' ', $classes);
        }
    }

    /**
     * listExtendQuery
     */
    public function listExtendQuery($query)
    {
        $query->withTrashed();
    }

    /**
     * formExtendQuery
     */
    public function formExtendQuery($query)
    {
        $query->withTrashed();
    }

    /**
     * formExtendFields displays username field if settings permit
     */
    public function formExtendFields($form)
    {
        $model = $form->getModel();

        // Show the username field if it is configured for use
        if (UserHelper::showUsername()) {
            $form->getField('username')?->hidden(false);
        }

        // Hide group fields for guests
        if ($model->is_guest) {
            $form->removeField('_group_ruler');
            $form->removeField('groups');
            $form->removeField('primary_group');
            $form->removeField('_password_ruler');
            $form->removeField('password');
            $form->removeField('password_confirmation');
            $form->removeField('is_two_factor_enabled');
        }

        if (!$model->is_two_factor_enabled) {
            $form->getField('is_two_factor_enabled')?->disabled();
        }
    }

    /**
     * formBeforeUpdate uses model events since dirty attributes are not available
     * after the form controller finishes saving
     */
    public function formBeforeUpdate($model)
    {
        $model->bindEvent('model.afterUpdate', fn() => $this->modelAfterUpdate($model));
    }

    /**
     * modelAfterCreate
     */
    public function formAfterCreate($model)
    {
        UserLog::createSystemRecord($model->getKey(), UserLog::TYPE_NEW_USER, [
            'user_full_name' => $model->full_name,
        ]);
    }

    /**
     * modelAfterUpdate
     */
    protected function modelAfterUpdate($model)
    {
        if ($model->isDirty('email')) {
            UserLog::createSystemRecord($model->getKey(), UserLog::TYPE_SET_EMAIL, [
                'user_full_name' => $model->full_name,
                'old_value' => $model->getOriginal('email'),
                'new_value' => $model->email,
            ]);
        }

        if ($model->isDirty('password')) {
            UserLog::createSystemRecord($model->getKey(), UserLog::TYPE_SET_PASSWORD, [
                'user_full_name' => $model->full_name
            ]);
        }
    }
}
