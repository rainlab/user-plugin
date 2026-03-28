<?php foreach ($records as $record): ?>
    <?= $this->makePartial('list_body_row', ['record' => $record]) ?>
<?php endforeach ?>
