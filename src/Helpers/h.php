<?php
/**
 * Created by PhpStorm.
 * User: mirocow
 * Date: 14.03.2018
 * Time: 3:10
 */

namespace Helpers;

/**
 * Class H
 */
class h extends \mirocow\helpers\HtmlHelper
{
    public static function url($url, $full = false)
    {
        if(is_array($url)){
            $route = array_shift($url);
            $params = $url;
        } else {
            $route = $url;
            $params = [];
        }

        if(!$route){
            return '';
        }

        return \Application::$dispatcher->url($route, $params, $full);
    }

}