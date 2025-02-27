<?php namespace RainLab\User\ContentFields;

use Tailor\Classes\ContentFieldBase;
use October\Contracts\Element\FormElement;
use October\Contracts\Element\ListElement;
use October\Contracts\Element\FilterElement;
use Tailor\Classes\Relations\CustomMultiJoinRelation;
use RainLab\User\Models\User;

/**
 * UsersField Content Field
 *
 * @link https://docs.octobercms.com/3.x/extend/tailor-fields.html
 */
class UsersField extends ContentFieldBase
{
    /**
     * @var int|null maxItems allowed
     */
    public $maxItems;

    /**
     * @var string displayMode for the relationship
     */
    public $displayMode = 'recordfinder';

    /**
     * defineConfig will process the field configuration.
     */
    public function defineConfig(array $config)
    {
        if (isset($config['maxItems'])) {
            $this->maxItems = (int) $config['maxItems'];
        }

        if (isset($config['displayMode'])) {
            $this->displayMode = (string) $config['displayMode'];
        }
    }

    /**
     * defineFormField will define how a field is displayed in a form.
     */
    public function defineFormField(FormElement $form, $context = null)
    {
        $field = $form->addFormField($this->fieldName, $this->label);

        // Singular and multi display modes
        $supportedDisplays = $this->maxItems === 1
            ? ['recordfinder']
            : ['taglist'];

        $field->displayAs(in_array($this->displayMode, $supportedDisplays)
            ? $this->displayMode
            : 'relation');

        if ($this->displayMode === 'controller') {
            $this->defineFormFieldAsRelationController($field);
        }

        // Used by relation and recordfinder
        $field
            ->list('~/plugins/rainlab/user/models/user/columns.yaml')
            ->nameFrom('full_name')
            ->descriptionFrom('email');

        if ($field->type === 'taglist') {
            // @deprecated this should be default
            $field->customTags(false);
            $field->nameFrom('email');
        }
    }

    /**
     * defineListColumn will define how a field is displayed in a list.
     */
    public function defineListColumn(ListElement $list, $context = null)
    {
        $partial = $this->maxItems === 1 ? 'column_single' : 'column_multi';

        $list->defineColumn($this->fieldName, $this->label)
            ->displayAs('partial')
            ->path("~/plugins/rainlab/user/contentfields/usersfield/partials/_{$partial}.php")
            ->clickable(false)
            ->sortable(false)
            ->shortLabel($this->shortLabel)
            ->useConfig($this->column ?: [])
        ;
    }

    /**
     * defineFilterScope will define how a field is displayed in a filter.
     */
    public function defineFilterScope(FilterElement $filter, $context = null)
    {
        $filter->defineScope($this->fieldName, $this->label)
            ->displayAs('group')
            ->nameFrom('email')
            ->shortLabel($this->shortLabel)
            ->useConfig($this->scope ?: [])
        ;
    }

    /**
     * extendModelObject will extend the record model.
     */
    public function extendModelObject($model)
    {
        $parentMultisite = $model->getBlueprintDefinition()->useMultisite();
        $isSingular = $this->maxItems === 1;

        if ($isSingular) {
            $model->belongsTo[$this->fieldName] = [
                User::class,
                'key' => $this->getSingularKeyName()
            ];
        }
        else {
            $model->morphToMany[$this->fieldName] = [
                User::class,
                'table' => $model->getBlueprintDefinition()->getJoinTableName(),
                'name' => $this->fieldName,
                'relationClass' => CustomMultiJoinRelation::class,
                'relatedKey' => 'id',
                'parentKey' => $parentMultisite ? 'site_root_id' : 'id'
            ];
        }
    }

    /**
     * extendDatabaseTable adds any required columns to the database.
     */
    public function extendDatabaseTable($table)
    {
        if ($this->maxItems === 1) {
            $table->integer($this->getSingularKeyName())->unsigned()->nullable();
        }
    }

    /**
     * getSingularKeyName
     */
    public function getSingularKeyName()
    {
        return $this->fieldName.'_id';
    }

    /**
     * defineFormFieldAsRelationController
     */
    protected function defineFormFieldAsRelationController($field)
    {
        $fieldConfig = [
            'label' => $this->label,
            'list' => '~/plugins/rainlab/user/models/user/columns.yaml',
            'form' => '~/plugins/rainlab/user/models/user/fields.yaml',
            'customMessages' => (array) $this->customMessages,
            'popupSize' => $this->popupSize,
            'view' => [
                'toolbarButtons' => 'add|remove',
                'recordsPerPage' => $this->recordsPerPage,
            ],
            'manage' => [
                'recordsPerPage' => $this->recordsPerPage,
            ]
        ];

        if ($this->span === 'adaptive') {
            $fieldConfig['externalToolbarAppState'] = 'toolbarExtensionPoint';
        }

        // Transfer custom configuration
        $toTransfer = ['label', 'list', 'form', 'view', 'manage'];
        foreach ($toTransfer as $transfer) {
            if (isset($this->controller[$transfer])) {
                $fieldConfig[$transfer] = is_array($this->controller[$transfer])
                    ? array_merge($fieldConfig[$transfer], (array) $this->controller[$transfer])
                    : $this->controller[$transfer];
            }
        }

        $field->controller($fieldConfig);
    }
}
