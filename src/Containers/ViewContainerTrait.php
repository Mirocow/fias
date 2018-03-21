<?php

namespace Containers;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * Trait ViewContainerTrait
 * @package Container
 */
trait ViewContainerTrait
{
    /** @var \View */
    static public $view;

    abstract protected function ensureParameters(array $config, array $parameterNames);

    /**
     * @param \ControllerInterface $controller
     */
    protected function loadViewConfig(\ControllerInterface $controller)
    {
        $viewsDirectory = $this->getViewsDirectory();
        $controller->setBasePath($viewsDirectory);
        $controller->setAlias('@app', $viewsDirectory);
    }

    /**
     * @param \ControllerInterface $controller
     * @return \View
     */
    protected function getView(\ControllerInterface $controller)
    {
        if (!self::$view) {
            $this->loadViewConfig($controller);
            self::$view = new \View($controller);
        }

        return self::$view;
    }

    /**
     * @return mixed|string
     * @throws \Exception
     */
    public function getDbUri()
    {
        return $this->getConfig('db.uri');
    }

    /**
     * @return string
     */
    public function getViewsDirectory()
    {
        return __DIR__ . '/../../app';
    }

    /**
     * @return string
     */
    public function getLayoutPath()
    {
        $viewsDirectory = $this->getViewsDirectory();
        return $viewsDirectory . '/Views/layouts';
    }

    /**
     * @return mixed|string
     * @throws \Exception
     */
    public function getHost()
    {
        return $this->getConfig('app.host');
    }
}
