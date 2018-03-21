<?php
/**
 * Created by PhpStorm.
 * User: mirocow
 * Date: 12.03.2018
 * Time: 5:39
 */

interface ViewContextInterface
{
    /**
     * @return string the view path that may be prefixed to a relative view name.
     */
    public function getViewPath();
}