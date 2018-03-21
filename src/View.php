<?php
/**
 * Created by PhpStorm.
 * User: mirocow
 * Date: 12.03.2018
 * Time: 4:49
 */

class View
{
    public $defaultExtension = 'php';

    private $_viewFiles = [];

    /** @var ControllerInterface */
    private $context;

    public function __construct($context)
    {
        $this->context = $context;
    }

    public function render($view, $params = [], $context = null)
    {
        $viewFile = $this->findViewFile($view, $context);
        return $this->renderFile($viewFile, $params, $context);
    }

    /**
     * @return mixed
     */
    public function getViewFile()
    {
        return end($this->_viewFiles);
    }

    protected function findViewFile($view, $context = null)
    {
        if (strncmp($view, '@', 1) === 0) {
            // e.g. "@app/views/main"
            $file = $this->context->getAlias($view);
        } elseif (strncmp($view, '//', 2) === 0) {
            // e.g. "//layouts/main"
            $file = $this->context->getViewPath() . DIRECTORY_SEPARATOR . ltrim($view, '/');
        } elseif (strncmp($view, '/', 1) === 0) {
            // e.g. "/site/index"
            if ($this->context !== null) {
                $file = $this->context->getViewPath() . DIRECTORY_SEPARATOR . ltrim($view, '/');
            } else {
                throw new \Exceptions\InvalidCallException("Unable to locate view file for view '$view': no active controller.");
            }
        } elseif ($context instanceof \ViewContextInterface) {
            $file = $context->getViewPath() . DIRECTORY_SEPARATOR . $view;
        } elseif (($currentViewFile = $this->getViewFile()) !== false) {
            $file = dirname($currentViewFile) . DIRECTORY_SEPARATOR . $view;
        } else {
            throw new \Exceptions\InvalidCallException("Unable to resolve view file for view '$view': no active view context.");
        }
        if (pathinfo($file, PATHINFO_EXTENSION) !== '') {
            return $file;
        }
        $path = $file . '.' . $this->defaultExtension;
        if ($this->defaultExtension !== 'php' && !is_file($path)) {
            $path = $file . '.php';
        }
        return $path;
    }

    public function renderFile($viewFile, $params = [], $context = null)
    {
        $viewFile = $this->context->getAlias($viewFile);
        if (!is_file($viewFile)) {
            throw new \Exceptions\ViewNotFoundException("The view file does not exist: $viewFile");
        }
        $oldContext = $this->context;
        if ($context !== null) {
            $this->context = $context;
        }
        $output = '';
        $this->_viewFiles[] = $viewFile;
        if ($this->beforeRender($viewFile, $params)) {
            $output = $this->renderPhpFile($viewFile, $params);
            $this->afterRender($viewFile, $params, $output);
        }
        array_pop($this->_viewFiles);
        $this->context = $oldContext;
        return $output;
    }

    public function beforeRender($viewFile, $params)
    {
        return true;
    }

    /**
     * @param $_file_
     * @param array $_params_
     * @return string
     * @throws Exception
     * @throws Throwable
     */
    public function renderPhpFile($_file_, $_params_ = [])
    {
        $_obInitialLevel_ = ob_get_level();
        ob_start();
        ob_implicit_flush(false);
        extract($_params_, EXTR_OVERWRITE);
        try {
            require $_file_;
            return ob_get_clean();
        } catch (\Exception $e) {
            while (ob_get_level() > $_obInitialLevel_) {
                if (!@ob_end_clean()) {
                    ob_clean();
                }
            }
            throw $e;
        } catch (\Throwable $e) {
            while (ob_get_level() > $_obInitialLevel_) {
                if (!@ob_end_clean()) {
                    ob_clean();
                }
            }
            throw $e;
        }
    }

    public function afterRender($viewFile, $params, &$output)
    {

    }

}