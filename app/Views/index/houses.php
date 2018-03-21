<ul>
    <?php foreach ($items as $item):?>
        <li><?= \Helpers\h::a($item['full_number'], ['\\Controllers\\IndexController@actionHouse', 'guid' => $item['id']])?></li>
    <?php endforeach;?>
</ul>
