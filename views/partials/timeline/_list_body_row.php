<?php
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
                <div class="text-muted text-nowrap ms-3">
                    <?= $timeTense ?>
                </div>
            </div>
        </div>
    </div>
</li>
