<?php
/**
 * Created by PhpStorm.
 * User: mirocow
 * Date: 11.03.2018
 * Time: 21:37
 */

return [
    //'GET /x1' => 'IndexController',
    //'GET,POST /x2' => 'IndexController',
    //'POST /x3' => 'IndexController',
    //'/fias' => 'IndexController',
    //'/user/{id:\d+}/{name}' => 'IndexController',
    //'/x/{id:\d+}/{name}' => '\\Controllers\\IndexController@actionDefault',
    '/fias' => '\\Controllers\\IndexController@actionIndex',
    '/fias/{guid:guid}' => '\\Controllers\\IndexController@actionReestr',
    '/fias/level/{guid:guid}' => '\\Controllers\\IndexController@actionLevel',
    '/fias/house/{guid:guid}' => '\\Controllers\\IndexController@actionHouse',
];