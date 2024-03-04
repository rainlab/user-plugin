<?php namespace RainLab\User\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use RainLab\User\Models\MailBlocker;
use RainLab\User\Models\Setting as UserSetting;

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
    public $relationConfig;

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
        if (
            UserSetting::get('login_attribute') == UserSetting::LOGIN_USERNAME &&
            array_key_exists('username', $form->getFields())
        ) {
            $form->getField('username')->hidden = false;
        }

        // Hide group fields for guests
        if ($model->is_guest) {
            $form->removeField('groups');
            $form->removeField('primary_group');
        }
    }

    /**
     * formAfterCreate automatically activates a user, if needed
     */
    public function formAfterCreate($model)
    {
        if (UserSetting::get('activate_mode') === UserSetting::ACTIVATE_AUTO) {
            $model->markEmailAsVerified();
        }
    }

    /**
     * formAfterUpdate
     */
    public function formAfterUpdate($model)
    {
        $blockMail = post('User[block_mail]', false);
        if ($blockMail !== false) {
            $blockMail ? MailBlocker::blockAll($model) : MailBlocker::unblockAll($model);
        }
    }

    /**
     * formExtendModel
     */
    public function formExtendModel($model)
    {
        $model->block_mail = MailBlocker::isBlockAll($model);

        $model->bindEvent('model.saveInternal', function() use ($model) {
            unset($model->attributes['block_mail']);
        });
    }
}
