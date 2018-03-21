<ul>
    <?php foreach ($items as $item):?>
        <li><?= \Helpers\AddressBuildHelper::build($item)?></li>
    <?php endforeach;?>
</ul>
