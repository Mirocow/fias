<?php
/**
 * Created by PhpStorm.
 * User: mirocow
 * Date: 16.03.2018
 * Time: 3:40
 */

namespace Helpers;

/**
‌{
    "id": "dff8590c-0a5c-4a7c-a2bd-48779e042316",
    "house_id": "1a079adc-9876-4eb8-869f-46b5680d77a9",
    "flat_number": "1",
    "postal_code": null,
    "cadastr_number": "77:06:0003013:14564",
    "address_id": "fde385c7-5a14-4ef4-befb-961fcc274c5d",
    "number": "30",
    "full_number": "30\u043a1",
    "building": "1",
    "structure": null,
    "parent_id": "0c5b2444-70a0-4932-980c-b4dc0d3f02b5",
    "level": "1",
    "address_level": "7",
    "house_count": "257",
    "next_address_level": "0",
    "title": "\u041d\u043e\u0432\u0430\u0442\u043e\u0440\u043e\u0432",
    "full_title": "\u0433 \u041c\u043e\u0441\u043a\u0432\u0430, \u0443\u043b \u041d\u043e\u0432\u0430\u0442\u043e\u0440\u043e\u0432",
    "region": "77",
    "prefix": "\u0443\u043b"
}*/
class AddressBuildHelper
{
    static public function build($item = [])
    {
        $items = [];
        $items[] = $item['full_title'];
        $items[] = "д. {$item['number']}";
        if(!empty($item['building'])) {
            $items[] = "корп. {$item['building']}";
        }
        if(!empty($item['structure'])) {
            $items[] = "стр. {$item['structure']}";
        }
        $items[] = "кв. {$item['flat_number']}";
        $items[] = "кадастр. {$item['cadastr_number']}";

        // Москва, р-н Обручевский, ул Новаторов, д 28, кв 65 Помещение 46.3 кв.м
        return implode(', ', $items);
    }
}