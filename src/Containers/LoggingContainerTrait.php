<?php

namespace Containers;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * Trait LoggingContainerTrait
 * @package Container
 */
trait LoggingContainerTrait
{
    /** @var Logger */
    static public $log; // fucking php can actually have private names conflict with other fucking private names

    private $stream;

    abstract protected function ensureParameters(array $config, array $parameterNames);

    protected function loadLoggingConfig()
    {
        $configDirectory = $this->getConfigDirectory();
        $this->stream = $configDirectory . $this->getConfig('logging.error_log');
    }

    /**
     * @return Logger
     */
    public function getLogger()
    {
        if (!self::$log) {
            $this->loadLoggingConfig();

            self::$log = new Logger('errors');
            self::$log->pushHandler(
                new StreamHandler($this->stream, Logger::DEBUG)
            );

        }

        return self::$log;
    }
}
