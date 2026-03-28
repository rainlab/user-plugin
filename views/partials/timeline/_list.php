<div
    class="control-list"
    data-control="listwidget">
    <?php if (count($records)): ?>
        <div class="element-timeline py-4 px-4">
            <ul class="timeline">
                <?= $this->makePartial('list_body_rows') ?>
            </ul>
        </div>
    <?php else: ?>
        <p class="no-data">
            <?= $noRecordsMessage ?>
        </p>
    <?php endif ?>

    <?php if ($showPagination): ?>
        <div class="list-footer">
            <?php if ($showPageNumbers): ?>
                <?= $this->makePartial('list_pagination') ?>
            <?php else: ?>
                <?= $this->makePartial('list_pagination_simple') ?>
            <?php endif ?>
        </div>
    <?php endif ?>
</div>
