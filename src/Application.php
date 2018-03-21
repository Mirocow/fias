<?php

use Loader\InitLoader;
use Loader\UpdateLoader;

class Application
{
    use \Containers\DbContainerTrait;
    use \Containers\LoggingContainerTrait;
    use \Containers\RouteContainerTrait;
    use \Containers\ViewContainerTrait;
    use \Containers\ParserContainerTrait;

    private $config = [];

    /**
     * Container constructor.
     */
    public function __construct()
    {
        $this->setErrorHandler();
        $configDirectory = $this->getConfigDirectory();
        $this->config = parse_ini_file($configDirectory . '/config.ini', true);
    }

    /**
     * @param array $config
     * @param array $parameterNames
     */
    protected function ensureParameters(array $config, array $parameterNames)
    {
        $undefinedMessages = [];
        foreach ($parameterNames as $name) {
            if (!isset($config[$name])) {
                $undefinedMessages[] = "Config parameter {$name} is not defined";
            }
        }

        if (count($undefinedMessages) > 0) {
            throw new \LogicException(implode("\n", $undefinedMessages));
        }
    }

    /**
     * @return string
     */
    public function getConfigDirectory()
    {
        return __DIR__ . '/../config';
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getConfig($name)
    {
        $this->ensureParameters($this->config, [$name]);
        return $this->config[$name];
    }

    /**
     * @return string
     */
    public function getRootDirectory()
    {
        return __DIR__ . '/..';
    }

    private function setErrorHandler()
    {
        ini_set("display_errors", "on");
        error_reporting(E_ALL);
        ini_set('html_errors', 'on');
        set_error_handler([$this, "handle_error"]);
    }

    function getDebugBacktrace($NL = "<BR>") {
        $dbgTrace = debug_backtrace();
        $dbgMsg = $NL."Debug backtrace begin:$NL";
        foreach($dbgTrace as $dbgIndex => $dbgInfo) {
            $dbgMsg .= "\t at $dbgIndex  ".$dbgInfo['file']." (line {$dbgInfo['line']}) -> {$dbgInfo['function']}(".join(",",$dbgInfo['args']).")$NL";
        }
        $dbgMsg .= "Debug backtrace end".$NL;
        return $dbgMsg;
    }

    function handle_error($errno, $errstr, $errfile, $errline)
    {
        if ($errno & E_ALL) {
            $errorType = '';
            switch ($errno) {
                case E_WARNING:
                case E_ERROR:
                case E_USER_ERROR:
                case E_USER_WARNING:
                case E_USER_NOTICE:
                    $errorType = "Errors: ";
                    break;
            }
            echo "{$errorType} {$errstr}<br>" . PHP_EOL;
            echo "{$errfile} ({$errline})<br>"  . PHP_EOL;
            echo $this->getDebugBacktrace();
            exit;
        }
    }

}
