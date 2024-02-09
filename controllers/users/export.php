<?php Block::put('breadcrumb') ?>
    <ul>
        <li><a href="<?= Backend::url('user/users') ?>"><?= __("Users") ?></a></li>
        <li><?= e(trans($this->pageTitle)) ?></li>
    </ul>
<?php Block::endPut() ?>

<?= Form::open(['class' => 'd-flex flex-column h-100']) ?>

    <div class="flex-grow-1">
        <?= $this->exportRender() ?>
    </div>

    <div class="form-buttons">
        <div class="loading-indicator-container">
            <?= Ui::popupButton("Export Users", 'onExportLoadForm')->keyboard(false)->primary()->icon('icon-cloud-download') ?>
        </div>
    </div>

<?= Form::close() ?>
