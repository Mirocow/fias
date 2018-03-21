<?php
/**
 * Created by PhpStorm.
 * User: mirocow
 * Date: 12.03.2018
 * Time: 0:25
 */

class Controller extends \BaseController implements ControllerInterface, ViewContextInterface
{
    /** @var View */
    private $view;

    /** @var Application */
    public $container;

    public $id;

    protected $layout = 'main';

    /**
     * Controller constructor.
     */
    public function __construct(Application $container)
    {
        $this->container = $container;
        $classname = $this->className();
        $this->id = strtolower(str_replace('Controller', '', (substr($classname, strrpos($classname, '\\') + 1))));
        $this->init();
    }

    /**
     * @return string
     */
    public function getViewPath()
    {
        if ($this->_viewPath === null) {
            $this->_viewPath = parent::getViewPath() . DIRECTORY_SEPARATOR . $this->id;;
        }
        return $this->_viewPath;
    }

    /**
     * @param View $view
     */
    public function setView(View $view)
    {
        $this->view = $view;
    }

    /**
     * @return View
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index', 'Run ' . get_class($this) . '@index');
    }

    /**
     *
     */
    public function afterAction()
    {
        // TODO: Implement afterAction() method.
    }

    /**
     *
     */
    public function beforeAction()
    {
        // TODO: Implement beforeAction() method.
    }

    /**
     * @param $content
     * @return string
     */
    public function renderContent($content)
    {
        $layoutFile = $this->findLayoutFile($this->view);
        if ($layoutFile !== false) {
            return $this->view->renderFile($layoutFile, ['content' => $content], $this);
        }

        return $content;
    }

    /**
     * @return string
     */
    public function getLayoutPath()
    {
        return $this->container->getLayoutPath();
    }

    /**
     * @param View $view
     * @return bool|mixed|string
     */
    public function findLayoutFile(View $view)
    {
        if (is_string($this->layout)) {
            $layout = $this->layout;
        }

        if (!isset($layout)) {
            return false;
        }

        if (strncmp($layout, '@', 1) === 0) {
            $file = $this->getAlias($layout);
        } elseif (strncmp($layout, '/', 1) === 0) {
            $file = $this->container->getLayoutPath() . DIRECTORY_SEPARATOR . substr($layout, 1);
        } else {
            $file = $this->getLayoutPath() . DIRECTORY_SEPARATOR . $layout;
        }
        if (pathinfo($file, PATHINFO_EXTENSION) !== '') {
            return $file;
        }
        $path = $file . '.' . $view->defaultExtension;
        if ($view->defaultExtension !== 'php' && !is_file($path)) {
            $path = $file . '.php';
        }
        return $path;
    }

    /**
     * @param $view
     * @param array $params
     * @param null $context
     * @return string
     */
    public function render($view, $params = [], $context = null)
    {
        if(!$context){
            $context = $this;
        }

        $content = $this->view->render($view, $params, $context);
        return $this->renderContent($content);
    }

}