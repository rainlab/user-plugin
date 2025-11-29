<?php if ($value): ?>
    <ul class="list-link-list">
        <?php foreach ($value as $entry): ?>
            <?php
                $url = Backend::url('user/users/preview/'.$entry->id);
            ?>
            <li><a href="<?= $url ?>"><?= e($entry->full_name) ?></a></li>
        <?php endforeach ?>
    </ul>
<?php endif ?>
