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

    <?php if ($this->user->hasAccess('rainlab.users.access_groups')): ?>
        <div class="toolbar-divider"></div>

        <?= Ui::button("User Groups", 'user/usergroups')
            ->icon('icon-group')
            ->secondary() ?>
    <?php endif ?>

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

    <div class="dropdown dropdown-fixed">
        <?= Ui::button("More Actions")
            ->attributes(['data-toggle' => 'dropdown'])
            ->circleIcon('icon-ellipsis-v')
            ->secondary()
        ?>
        <ul class="dropdown-menu">
            <li>
                <?= Ui::ajaxButton("Activate", 'onActivateSelected')
                    ->replaceCssClass('dropdown-item')
                    ->listCheckedTrigger()
                    ->listCheckedRequest()
                    ->icon('icon-user-plus')
                    ->secondary()
                    ->confirmMessage("Are you sure?") ?>
            </li>
            <li>
                <?= Ui::ajaxButton("Restore", 'onRestoreSelected')
                    ->replaceCssClass('dropdown-item')
                    ->listCheckedTrigger()
                    ->listCheckedRequest()
                    ->icon('icon-star')
                    ->secondary()
                    ->confirmMessage("Are you sure?") ?>
            </li>
            <li role="separator" class="dropdown-divider"></li>
            <li>
                <?= Ui::ajaxButton("Ban", 'onBanSelected')
                    ->replaceCssClass('dropdown-item')
                    ->listCheckedTrigger()
                    ->listCheckedRequest()
                    ->icon('icon-ban')
                    ->secondary()
                    ->confirmMessage("Are you sure?") ?>
            </li>
            <li>
                <?= Ui::ajaxButton("Unban", 'onUnbanSelected')
                    ->replaceCssClass('dropdown-item')
                    ->listCheckedTrigger()
                    ->listCheckedRequest()
                    ->icon('icon-circle-o-notch')
                    ->secondary()
                    ->confirmMessage("Are you sure?") ?>
            </li>
            <?php /*
            <li role="separator" class="dropdown-divider"></li>
            <li>
                <?= Ui::button("Import", 'rainlab/user/users/import')
                    ->replaceCssClass('dropdown-item')
                    ->icon('icon-upload') ?>
            </li>
            <li>
                <?= Ui::button("Export", 'rainlab/user/users/export')
                    ->replaceCssClass('dropdown-item')
                    ->icon('icon-download') ?>
            </li>
            */ ?>
        </ul>
    </div>
</div>
