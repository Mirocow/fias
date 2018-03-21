<?php

namespace Containers;

use Grace\DBAL\ConnectionAbstract\ConnectionInterface;
use Grace\DBAL\ConnectionFactory;

/**
 * Trait DbContainerTrait
 * @package Container
 */
trait DbContainerTrait
{
    /** @var ConnectionInterface */
    static public $db; // fucking php can actually have private names conflict with other fucking private names

    private $uriInDbTrait;

    abstract protected function ensureParameters(array $config, array $parameterNames);

    protected function loadDbConfig()
    {
        $this->uriInDbTrait = $this->getConfig('db.uri');
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getDbConfig()
    {
        $dbUri = $this->getDbUri();

        return parse_url($dbUri);
    }

    /**
     * @return mixed
     */
    public function getDatabaseName()
    {
        $parts = explode('/', $this->getConfig('db.uri'));

        return array_pop($parts);
    }

    /**
     * @return ConnectionInterface
     */
    public function getDb()
    {
        if (!self::$db) {
            $this->loadDbConfig();

            $uri = parse_url($this->uriInDbTrait);

            self::$db = ConnectionFactory::getConnection(array(
                'adapter'  => ($uri['scheme'] == 'mysql') ? 'mysqli' : $uri['scheme'],
                'host'     => $uri['host'],
                'port'     => $uri['port'],
                'user'     => $uri['user'],
                'password' => $uri['pass'],
                'database' => trim($uri['path'], '/'),
            ));
        }
        return self::$db;
    }

}
