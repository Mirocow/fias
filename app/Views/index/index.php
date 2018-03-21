<ul>
<?php foreach ($items as $item):?>
    <li><?= \Helpers\h::a($item['title'], ['\\Controllers\\IndexController@actionReestr', 'guid' => $item['address_id']])?></li>
<?php endforeach;?>
</ul>
