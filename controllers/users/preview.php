<?php Block::put('breadcrumb') ?>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= Backend::url('user/users') ?>"><?= __("Users") ?></a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= e(__($this->pageTitle)) ?></li>
    </ol>
<?php Block::endPut() ?>

<?php if ($this->fatalError): ?>
    <?= $this->formRenderDesign() ?>
<?php else: ?>

<div class="scoreboard" id="<?= $this->getId('scoreboard') ?>">
    <?= $this->makePartial('scoreboard_preview') ?>
</div>

<?php
    $canDoGeneralActions = !$formModel->is_guest && !$formModel->is_banned && !$formModel->trashed();
?>
<div class="loading-indicator-container mb-3">
    <div class="control-toolbar form-toolbar" data-control="toolbar">
        <?= Ui::button("Back", 'user/users')->icon('icon-arrow-left')->outline() ?>
        <?php if ($canDoGeneralActions): ?>
            <?php if ($this->user->hasAccess('rainlab.users.impersonate_user')): ?>
                <div class="toolbar-divider"></div>
                <?= Ui::ajaxButton("Impersonate", 'onImpersonateUser')->icon('icon-user-secret')->outline()
                    ->confirmMessage("Impersonate this user? You can revert to your original state by logging out.") ?>
            <?php endif ?>
        <?php endif ?>

        <?= Ui::button("Edit", "user/users/update/{$formModel->id}")->icon('icon-pencil')->outline()->primary() ?>

        <div class="toolbar-divider"></div>

        <?php if ($formModel->is_guest): ?>
            <?= Ui::popupButton("Convert to Registered", 'onLoadConvertGuestForm')->icon('icon-user')->outline()->info() ?>
            <div class="toolbar-divider"></div>
        <?php endif ?>

        <?php if ($canDoGeneralActions): ?>
            <?= Ui::ajaxButton("Ban", 'onBanUser')->icon('icon-ban')->outline()->danger()
                ->confirmMessage("Ban this user? It will prevent them from logging in and holding an active session.") ?>
        <?php endif ?>

        <?php if (!$formModel->trashed()): ?> ?>
            <?= Ui::ajaxButton("Delete", 'onDelete')->icon('icon-delete')->outline()->danger()
                ->confirmMessage("Are you sure?") ?>
        <?php endif ?>
        <?=
            /**
             * @event rainlab.user.view.extendPreviewToolbar
             * Fires when preview user toolbar is rendered.
             *
             * Example usage:
             *
             *     Event::listen('rainlab.user.view.extendPreviewToolbar', function (
             *         (RainLab\User\Controllers\Users) $controller,
             *         (RainLab\User\Models\User) $record
             *     ) {
             *         return $controller->makePartial('~/path/to/partial');
             *     });
             *
             */
            $this->fireViewEvent('rainlab.user.view.extendPreviewToolbar', [
                'record' => $formModel
            ]);
        ?>
    </div>
</div>

<?php if ($formModel->is_guest): ?>
    <?= $this->makePartial('hint_guest') ?>
<?php elseif ($formModel->is_banned): ?>
    <?= $this->makePartial('hint_banned') ?>
<?php elseif ($formModel->trashed()): ?>
    <?= $this->makePartial('hint_trashed') ?>
<?php elseif (!$formModel->is_activated): ?>
    <?= $this->makePartial('hint_activate') ?>
<?php endif ?>

<?php
    /**
     * @event rainlab.user.view.extendPreviewTabs
     * Provides an opportunity to add tabs to the user preview page in the admin panel.
     * The event should return an array of `[Tab Name => ~/path/to/partial.php]`
     *
     * Example usage:
     *
     *   Event::listen('rainlab.user.view.extendPreviewTabs', function() {
     *       return [
     *           "Orders" => '$/acme/shop/partials/_user_orders.php',
     *       ];
     *   });
     *
     */
    $customTabs = array_collapse(Event::fire('rainlab.user.view.extendPreviewTabs'));
?>
<div class="control-tabs content-tabs tabs-inset" data-control="tab">
    <ul class="nav nav-tabs">
        <li class="active">
            <a href="#user">
                <?= __("User") ?>
            </a>
        </li>

        <?php if ($this->user->hasAccess('rainlab.user.timelines')): ?>
            <li>
                <a href="#history">
                    <?= __("History") ?>
                </a>
            </li>
        <?php endif ?>

        <?php foreach ($customTabs as $tabName => $tabPartial): ?>
            <li>
                <a href="#<?= Str::slug(__($tabName)) ?>">
                    <?= e(__($tabName)) ?>
                </a>
            </li>
        <?php endforeach ?>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active">
            <div class="row">
                <div class="col">
                    <h4 class="my-3 fw-normal"><?= __("User") ?></h4>
                    <?= $this->formRenderPrimaryTab('User') ?>
                </div>
                <div class="col border-start">
                    <h4 class="my-3 fw-normal"><?= __("Details") ?></h4>
                    <?= $this->formRenderPrimaryTab('Details') ?>
                </div>
            </div>
        </div>

        <?php if ($this->user->hasAccess('rainlab.user.timelines')): ?>
            <div class="tab-pane">
                <h4 class="my-3 fw-normal">Activity Log</h4>
                <?= $this->relationRender('activity_log') ?>
            </div>
        <?php endif ?>

        <?php foreach ($customTabs as $tabName => $tabPartial): ?>
            <div class="tab-pane">
                <?= $this->makePartial($tabPartial) ?>
            </div>
        <?php endforeach ?>
    </div>
</div>

<?php endif ?>
