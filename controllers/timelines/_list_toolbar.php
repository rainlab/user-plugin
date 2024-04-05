<div data-control="toolbar loader-container">
    <?= Ui::ajaxButton("Refresh", 'onRefreshList')
        ->loadingMessage("Updating Activity Timeline...")
        ->icon('icon-refresh')
        ->secondary() ?>
</div>
