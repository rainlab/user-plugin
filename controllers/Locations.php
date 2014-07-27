<?php namespace RainLab\User\Controllers;

use Lang;
use Flash;
use Backend;
use Redirect;
use BackendMenu;
use RainLab\User\Models\Country;
use Backend\Classes\Controller;
use System\Classes\SettingsManager;

/**
 * Locations Back-end Controller
 */
class Locations extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.RelationController',
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';
    public $relationConfig = 'config_relation.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('October.System', 'system', 'settings');
        SettingsManager::setContext('RainLab.User', 'location');
    }

    /**
     * {@inheritDoc}
     */
    public function listInjectRowClass($record, $definition = null)
    {
        if (!$record->is_enabled)
            return 'safe disabled';
    }

    public function onLoadDisableForm()
    {
        try {
            $this->vars['checked'] = post('checked');
        }
        catch (Exception $ex) {
            $this->handleError($ex);
        }

        return $this->makePartial('disable_form');
    }

    public function onDisableLocations()
    {
        $enable = post('enable', false);
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {

            foreach ($checkedIds as $objectId) {
                if (!$object = Country::find($objectId))
                    continue;

                $object->is_enabled = $enable;
                $object->save();
            }

        }

        if ($enable)
            Flash::success(Lang::get('rainlab.user::lang.locations.enable_success'));
        else
            Flash::success(Lang::get('rainlab.user::lang.locations.disable_success'));

        return Redirect::to(Backend::url('rainlab/user/locations'));
    }
}