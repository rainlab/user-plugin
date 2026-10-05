<?php $requireApproval = \RainLab\User\Models\Setting::get('require_approval'); ?>
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

    <div class="dropdown dropdown-fixed">
        <?= Ui::button("Manage")
            ->attributes(['data-toggle' => 'dropdown'])
            ->listCheckedTrigger()
            ->icon('icon-angle-down')
            ->secondary() ?>
        <ul class="dropdown-menu">
            <?php if ($requireApproval): ?>
                <li>
                    <?= Ui::ajaxButton("Approve", 'onApproveSelected')
                        ->replaceCssClass('dropdown-item')
                        ->listCheckedRequest()
                        ->icon('icon-check')
                        ->confirmMessage("Are you sure?") ?>
                </li>
                <li role="separator" class="dropdown-divider"></li>
            <?php endif ?>
            <li>
                <?= Ui::ajaxButton("Activate", 'onActivateSelected')
                    ->replaceCssClass('dropdown-item')
                    ->listCheckedRequest()
                    ->icon('icon-user-plus')
                    ->confirmMessage("Are you sure?") ?>
            </li>
            <li>
                <?= Ui::ajaxButton("Restore", 'onRestoreSelected')
                    ->replaceCssClass('dropdown-item')
                    ->listCheckedRequest()
                    ->icon('icon-star')
                    ->confirmMessage("Are you sure?") ?>
            </li>
            <li role="separator" class="dropdown-divider"></li>
            <li>
                <?= Ui::ajaxButton("Ban", 'onBanSelected')
                    ->replaceCssClass('dropdown-item')
                    ->listCheckedRequest()
                    ->icon('icon-ban')
                    ->confirmMessage("Are you sure?") ?>
            </li>
            <li>
                <?= Ui::ajaxButton("Unban", 'onUnbanSelected')
                    ->replaceCssClass('dropdown-item')
                    ->listCheckedRequest()
                    ->icon('icon-circle-o-notch')
                    ->confirmMessage("Are you sure?") ?>
            </li>
            <li role="separator" class="dropdown-divider"></li>
            <li>
                <?= Ui::popupButton("Merge Users", 'onLoadMergeUsersForm')
                    ->replaceCssClass('dropdown-item')
                    ->listCheckedRequest()
                    ->icon('icon-compress') ?>
            </li>
        </ul>
    </div>

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
            ->secondary() ?>
        <ul class="dropdown-menu">
            <li>
                <?= Ui::button("Import", 'user/users/import')
                    ->replaceCssClass('dropdown-item')
                    ->icon('icon-upload') ?>
            </li>
            <li>
                <?= Ui::button("Export", 'user/users/export')
                    ->replaceCssClass('dropdown-item')
                    ->icon('icon-download') ?>
            </li>
        </ul>
    </div>
</div>
