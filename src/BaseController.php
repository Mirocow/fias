<?php
/**
 * Created by PhpStorm.
 * User: mirocow
 * Date: 12.03.2018
 * Time: 6:02
 */

class BaseController
{
    private $aliases = [];

    protected $_basePath;

    protected $_viewPath;

    /**
     * @return string
     */
    public function className()
    {
        return get_class($this);
    }

    public function init()
    {

    }

    /**
     * @param $alias
     * @param bool $throwException
     * @return bool|mixed|string
     */
    public function getAlias($alias, $throwException = true)
    {
        if (strncmp($alias, '@', 1)) {
            // not an alias
            return $alias;
        }
        $pos = strpos($alias, '/');
        $root = $pos === false ? $alias : substr($alias, 0, $pos);
        if (isset($this->aliases[$root])) {
            if (is_string($this->aliases[$root])) {
                return $pos === false ? $this->aliases[$root] : $this->aliases[$root] . substr($alias, $pos);
            }
            foreach ($this->aliases[$root] as $name => $path) {
                if (strpos($alias . '/', $name . '/') === 0) {
                    return $path . substr($alias, strlen($name));
                }
            }
        }
        if ($throwException) {
            throw new InvalidArgumentException("Invalid path alias: $alias");
        }
        return false;
    }

    /**
     * @param $alias
     * @return bool|int|string
     */
    public function getRootAlias($alias)
    {
        $pos = strpos($alias, '/');
        $root = $pos === false ? $alias : substr($alias, 0, $pos);
        if (isset($this->aliases[$root])) {
            if (is_string($this->aliases[$root])) {
                return $root;
            }
            foreach ($this->aliases[$root] as $name => $path) {
                if (strpos($alias . '/', $name . '/') === 0) {
                    return $name;
                }
            }
        }
        return false;
    }

    /**
     * @param $alias
     * @param $path
     */
    public function setAlias($alias, $path)
    {
        if (strncmp($alias, '@', 1)) {
            $alias = '@' . $alias;
        }
        $pos = strpos($alias, '/');
        $root = $pos === false ? $alias : substr($alias, 0, $pos);
        if ($path !== null) {
            $path = strncmp($path, '@', 1) ? rtrim($path, '\\/') : $this->getAlias($path);
            if (!isset($this->aliases[$root])) {
                if ($pos === false) {
                    $this->aliases[$root] = $path;
                } else {
                    $this->aliases[$root] = [$alias => $path];
                }
            } elseif (is_string($this->aliases[$root])) {
                if ($pos === false) {
                    $this->aliases[$root] = $path;
                } else {
                    $this->aliases[$root] = [
                        $alias => $path,
                        $root => $this->aliases[$root],
                    ];
                }
            } else {
                $this->aliases[$root][$alias] = $path;
                krsort($this->aliases[$root]);
            }
        } elseif (isset($this->aliases[$root])) {
            if (is_array($this->aliases[$root])) {
                unset($this->aliases[$root][$alias]);
            } elseif ($pos === false) {
                unset($this->aliases[$root]);
            }
        }
    }

    /**
     * @param $path
     */
    public function setBasePath($path)
    {
        $path = $this->getAlias($path);
        $p = strncmp($path, 'phar://', 7) === 0 ? $path : realpath($path);
        if ($p !== false && is_dir($p)) {
            $this->_basePath = $p;
        } else {
            throw new InvalidArgumentException("The directory does not exist: $path");
        }
    }

    /**
     * @return string
     */
    public function getBasePath()
    {
        if ($this->_basePath === null) {
            $class = new \ReflectionClass($this);
            $this->_basePath = dirname($class->getFileName());
        }
        return $this->_basePath;
    }

    /**
     * @return string
     */
    public function getViewPath()
    {
        if ($this->_viewPath === null) {
            $this->_viewPath = $this->getBasePath() . DIRECTORY_SEPARATOR . 'Views';
        }
        return $this->_viewPath;
    }

}