<?php

namespace Containers;

use Pinepain\SimpleRouting\NotFoundException;
use Pinepain\SimpleRouting\Solutions\SimpleRouter;
use Pinepain\SimpleRouting\Parser;
use Pinepain\SimpleRouting\RoutesCollector;
use Pinepain\SimpleRouting\Compiler;
use Pinepain\SimpleRouting\Filter;
use Pinepain\SimpleRouting\CompilerFilters\Formats;
use Pinepain\SimpleRouting\CompilerFilters\Helpers\FormatsCollection;
use Pinepain\SimpleRouting\RulesGenerator;
use Pinepain\SimpleRouting\Matcher;
use Pinepain\SimpleRouting\FormatsHandler;
use Pinepain\SimpleRouting\FormatHandlers\Path as PathFormatHandler;
use Pinepain\SimpleRouting\UrlGenerator;

/**
 * Trait RouteContainerTrait
 * @package Container
 */
trait RouteContainerTrait
{
    /** @var SimpleRouter */
    static public $dispatcher;

    /** @var array  */
    private $routes = [];

    private $cache;

    private $_validClassMethods = [];

    abstract protected function ensureParameters(array $config, array $parameterNames);

    protected function loadRouteConfig()
    {
        $configDirectory = $this->getConfigDirectory();
        $routes = require($configDirectory . '/routes.php');

        foreach ($routes as $route => $handler){
            $methods = '*';
            if(strpos($route, ' ') !== false){
                list($methods, $route) = explode(' ', $route);
            }
            if(strpos($methods, ',') !== false){
                $methods = explode(',', $methods);
            }
            $this->routes[] = [
                'methods' => $methods,
                'route' => $route,
                'handler' => $handler,
            ];
        }
    }

    /**
     * @return SimpleRouter
     */
    public function getDispatcher()
    {
        if (!self::$dispatcher) {
            $this->loadRouteConfig();

            $formats_preset = [
                ['segment', '[^/]+', ['default']],
                ['alpha', '[[:alpha:]]+', ['a']],
                ['digit', '[[:digit:]]+', ['d']],
                ['word', '[\w]+', ['w']],
                ['slug', '[a-z0-9]+(?:-[a-z0-9]+)*', ['s']],
                ['guid', '[\w\d-]+', ['guid']],
            ];

            $collector       = new RoutesCollector(new Parser());
            $filter          = new Filter([new Formats(new FormatsCollection($formats_preset))]);
            $generator       = new RulesGenerator($filter, new Compiler());
            $dispatcher      = new Matcher();
            $formats_handler = new FormatsHandler([new PathFormatHandler()]);
            $url_generator   = new UrlGenerator($formats_handler);
            self::$dispatcher = new SimpleRouter($collector, $generator, $dispatcher, $url_generator);

            foreach ($this->routes as $route){
                self::$dispatcher->add($route['route'], $route['handler']);
            }

        }

        return self::$dispatcher;
    }

    /**
     * @param SimpleRouter $dispatcher
     * @param string $uri
     */
    public function routeExecute(SimpleRouter $dispatcher, $uri)
    {
        try {
            $routeInfo = $dispatcher->match($uri);

            /** @var \ControllerInterface $controller */
            $controller = $routeInfo->handler;

            if (strpos($controller, '@') !== false) {
                list($controller, $method) = explode('@', $controller);
            }
            if (!class_exists($controller)) {
                $controller = \Controller::class;
            }
            if (empty($method)) {
                $method = 'actionIndex';
            }

            $controller = new $controller($this);
            $view = $this->getView($controller);
            $controller->setView($view);

            $this->execute($controller, 'beforeAction');
            $output = $this->execute($controller, $method, $routeInfo->variables);
            $this->execute($controller, 'afterAction');

            echo $output;
        } catch (NotFoundException $e) {
            throw new \Exception('404 Not Found');
        } catch (\Exception $e) {
            echo '<pre>';
            echo $e->getMessage() . PHP_EOL;
            echo $e->getFile() . ' ' . $e->getLine() . PHP_EOL;
            echo var_dump($e->getTrace()) . PHP_EOL;
            echo '</pre>';
            die();
        }

    }

    /**
     * @param $handler
     * @param $method
     * @param array $arguments
     * @return mixed
     * @throws \Exception
     */
    private function execute($handler, $method, $arguments = [])
    {
        if ($this->methodInClassValidate($handler->className(), $method)) {
            if($this->argumentsValidate($handler->className(), $method, $arguments)){
                return call_user_func_array([$handler, $method], $arguments);
            }
            throw new \Exception("Send wrong variables to method `{$method}` in class `{$handler->className()}`");
        } else {
            throw new \Exception("Method `{$method}` not exist in class `{$handler->className()}`");
        }
    }

    /**
     * @param $class
     * @param $method
     * @param $arguments
     * @return bool
     * @throws \Exception
     * @var \ReflectionFunction $refFunc
     */
    private function argumentsValidate($class, $method, $arguments)
    {
        if (isset($this->_validClassMethods[$class]) && isset($this->_validClassMethods[$class][$method])) {
            $refFunc = $this->_validClassMethods[$class][$method];
            $userArguments = array_keys($arguments);
            $missingArguments = [];
            foreach ($refFunc->getParameters() as $param) {
                if (!$param->isOptional() && !in_array($param->getName(), $userArguments)) {
                    $missingArguments[] = $param->getName();
                } else {
                    if ($param->getClass()
                        && (
                            !is_object($arguments[$param->getName()])
                            || get_class($arguments[$param->getName()]) !== $param->getClass()->name
                        )
                    ) {
                        throw new \Exception("Method `{$method}` param `{$param->getName()}` " .
                            "expects type `{$param->getClass()->name}` but got " . gettype($arguments[$param->getName()])
                        );
                    }
                }
            }
            if (sizeof($missingArguments)) {
                throw new \Exception(
                    "Method `{$method}` missing required arguments: " . implode(', ', $missingArguments));
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Проверка валидности котроллеров с кешированием результата проверки
     * @param $class
     * @param $method
     * @return bool
     */
    private function methodInClassValidate($class, $method)
    {
        $classHasMethod = false;

        if (isset($this->_validClassMethods[$class]) && isset($this->_validClassMethods[$class][$method])) {
            $classHasMethod = true;
        } elseif (method_exists($class, $method)
            && is_callable([$class, $method])
        ) {
            $this->_validClassMethods[$class] = [];
            $this->_validClassMethods[$class][$method] = new \ReflectionMethod($class, $method);;
            $classHasMethod = true;
        }

        return $classHasMethod;
    }
}
