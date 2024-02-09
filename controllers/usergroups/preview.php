<?php Block::put('breadcrumb') ?>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= Backend::url('user/users') ?>"><?= __("Users") ?></a></li>
        <li class="breadcrumb-item"><a href="<?= Backend::url('user/usergroups') ?>"><?= __("User Groups") ?></a></li>
        <li class="breadcrumb-item active"><?= e($this->pageTitle) ?></li>
    </ol>
<?php Block::endPut() ?>

<?php if (!$this->fatalError): ?>

    <div class="form-preview">
        <?= $this->formRenderPreview() ?>
    </div>

<?php else: ?>

    <p class="flash-message static error"><?= e($this->fatalError) ?></p>
    <p><a href="<?= Backend::url('rainlab/user/usergroups') ?>" class="btn btn-default">Return to user groups list</a></p>

<?php endif ?>