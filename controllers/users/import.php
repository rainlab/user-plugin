<?php Block::put('breadcrumb') ?>
    <ul>
        <li><a href="<?= Backend::url('user/users') ?>"><?= __("Users") ?></a></li>
        <li><?= e(trans($this->pageTitle)) ?></li>
    </ul>
<?php Block::endPut() ?>

<?= Form::open(['class' => 'd-flex flex-column h-100']) ?>

    <div class="flex-grow-1">
        <?= $this->importRender() ?>
    </div>

    <div class="form-buttons">
        <?= Ui::popupButton("Import Users", 'onImportLoadForm')->keyboard(false)->primary()->icon('icon-cloud-upload') ?>
    </div>

<?= Form::close() ?>
