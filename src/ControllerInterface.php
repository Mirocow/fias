<?php
/**
 * Created by PhpStorm.
 * User: mirocow
 * Date: 12.03.2018
 * Time: 0:28
 */

interface ControllerInterface
{

    public function actionIndex();

    public function beforeAction();

    public function afterAction();

    public function getAlias($alias, $throwException = true);

    public function getRootAlias($alias);

    public function setAlias($alias, $path);

    public function setBasePath($path);

    public function getBasePath();

    public function getViewPath();

    public function setView(View $view);

    public function getView();

}