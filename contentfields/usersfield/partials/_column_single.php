<?php if ($value): ?>
    <ul class="list-link-list">
        <?php
            $url = Backend::url('user/users/preview/'.$value->id);
        ?>
        <li><a href="<?= $url ?>"><?= e($value->full_name) ?></a></li>
    </ul>
<?php endif ?>
