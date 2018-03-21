<ul>
    <?php foreach ($items as $level => $objects):?>
        <p><?= $levels[$level]?></p>
        <?php foreach ($objects as $object):?>
            <li><?= \Helpers\h::a($object['objectlevel'] . ' ' . $object['title'], ['\\Controllers\\IndexController@actionLevel', 'guid' => $object['address_id']])?></li>
        <?php endforeach;?>
    <?php endforeach;?>
</ul>