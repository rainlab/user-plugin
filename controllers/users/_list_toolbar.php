<div data-control="toolbar">
    <?= Ui::button("New User", 'user/users/create')
        ->icon('icon-plus')
        ->primary() ?>

    <?= Ui::ajaxButton("Delete", 'onDeleteSelected')
        ->listCheckedTrigger()
        ->listCheckedRequest()
        ->icon('icon-delete')
        ->secondary()
        ->confirmMessage("Are you sure?") ?>

    <div class="toolbar-divider"></div>

    <?php Ui::dropdownButton(
        label: __("Manage"),
        icon: 'icon-angle-down',
        caret: false,
        secondary: true,
        dataListCheckedTrigger: true
    )->slot() ?>
        <?= Ui::dropdownItem(
            label: __("Activate"),
            handler: 'onActivateSelected',
            icon: 'icon-user-plus',
            dataListCheckedRequest: true,
            dataRequestConfirm: __("Are you sure?")
        ) ?>
        <?= Ui::dropdownItem(
            label: __("Restore"),
            handler: 'onRestoreSelected',
            icon: 'icon-star',
            dataListCheckedRequest: true,
            dataRequestConfirm: __("Are you sure?")
        ) ?>
        <?= Ui::dropdownDivider() ?>
        <?= Ui::dropdownItem(
            label: __("Ban"),
            handler: 'onBanSelected',
            icon: 'icon-ban',
            dataListCheckedRequest: true,
            dataRequestConfirm: __("Are you sure?")
        ) ?>
        <?= Ui::dropdownItem(
            label: __("Unban"),
            handler: 'onUnbanSelected',
            icon: 'icon-circle-o-notch',
            dataListCheckedRequest: true,
            dataRequestConfirm: __("Are you sure?")
        ) ?>
        <?= Ui::dropdownDivider() ?>
        <?= Ui::dropdownItem(
            label: __("Merge Users"),
            handler: 'onLoadMergeUsersForm',
            icon: 'icon-compress',
            dataControl: 'popup',
            dataListCheckedRequest: true
        ) ?>
    <?= Ui::end() ?>

    <?=
        /**
         * @event rainlab.user.view.extendListToolbar
         * Fires when user list toolbar is rendered.
         *
         * Example usage:
         *
         *     Event::listen('rainlab.user.view.extendListToolbar', function (
         *         (RainLab\User\Controllers\Users) $controller
         *     ) {
         *         return $controller->makePartial('~/path/to/partial');
         *     });
         *
         */
        $this->fireViewEvent('rainlab.user.view.extendListToolbar');
    ?>

    <?php Ui::dropdownButton(
        title: __("More Actions"),
        icon: 'icon-ellipsis-v',
        secondary: true,
        caret: false,
        class: 'btn-circle'
    )->slot() ?>
        <?= Ui::dropdownItem(
            label: __("Import"),
            href: Backend::url('user/users/import'),
            icon: 'icon-upload'
        ) ?>
        <?= Ui::dropdownItem(
            label: __("Export"),
            href: Backend::url('user/users/export'),
            icon: 'icon-download'
        ) ?>
    <?= Ui::end() ?>
</div>
