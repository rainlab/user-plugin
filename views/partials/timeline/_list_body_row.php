<?php
    $showUser = isset($columns['user_full_name']);
    $linkage = $showUser ? $record->user_backend_linkage : null;
    $timeTense = Backend::dateTime($record->created_at, [
        'defaultValue' => \System\Helpers\DateTime::timeTense($record->created_at),
        'timeTense' => true,
    ]);
?>
<li class="timeline-item">
    <div class="timeline-body">
        <div class="timeline-content">
            <div class="d-flex">
                <div class="flex-grow-1">
                    <?= $record->detail ?>
                </div>
                <?php if ($linkage): ?>
                    <div class="text-muted text-nowrap ms-3">
                        <a href="<?= $linkage[0] ?>">
                            <?= e($linkage[1]) ?>
                        </a>
                    </div>
                <?php endif ?>
                <div class="text-muted text-nowrap ms-3">
                    <?= $timeTense ?>
                </div>
            </div>
        </div>
    </div>
</li>
