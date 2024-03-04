<?php Block::put('breadcrumb') ?>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= Backend::url('user/users') ?>"><?= __("Users") ?></a></li>
        <?php if (isset($formModel)): ?><li class="breadcrumb-item"><a href="<?= Backend::url('user/users/preview/'.$formModel->id) ?>"><?= __("View User") ?></a></li><?php endif ?>
        <li class="breadcrumb-item active" aria-current="page"><?= e(__($this->pageTitle)) ?></li>
    </ol>
<?php Block::endPut() ?>

<?= $this->formRenderDesign() ?>
