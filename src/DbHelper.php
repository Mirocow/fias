<?php

use FileSystem\FileHelper;
use Grace\DBAL\ConnectionAbstract\ConnectionInterface;

class DbHelper
{
    private static $dataTypes = ['varchar', 'integer', 'uuid'];

    public static function createTable(ConnectionInterface $db, $name, $fields, $isTemp = true)
    {
        $sql    = '';
        $params = [$name];

        foreach ($fields as $field) {
            $params[] = $field['name'];
            $type     = !empty($field['type']) ? $field['type'] : 'varchar';

            if (!in_array($type, static::$dataTypes)) {
                throw new \LogicException('Некорректный тип: ' . $type);
            }

            $sql .= ', ?f ' . $type;
        }

        $sql = 'CREATE '
            . ($isTemp ? 'TEMP ' : '')
            . 'TABLE ?f ( '
            . substr($sql, 2)
            . ')'
        ;

        $db->execute($sql, $params);
    }

    public static function runFile(array $dbConfig, $path)
    {
        FileHelper::ensureIsReadable($path);
        $path = escapeshellarg($path);

        $command = '';
        if(isset($dbConfig['pass'])){
            $command .= 'set PGPASSWORD=' . $dbConfig['pass'] . ' &&';
        }
        $command .= ' psql ';
        if(isset($dbConfig['user'])){
            $command .= ' -U ' . $dbConfig['user'];
        }
        if(isset($dbConfig['path'])){
            $db = ltrim($dbConfig['path'], '/');
        }
        if(!isset($db)){
            throw new \Exception("Database not found");
        }
        $command .= ' -f ' . $path . ' ' . $db . ' 2>&1';

        exec($command, $output, $result);

        if ($result !== 0) {
            throw new \Exception("Ошибка выполнения SQL файла: \n" . implode("\n", $output) . "\n\n");
        }

        if ($output) {
            foreach ($output as $line) {
                if (preg_match('/psql:(.*)ERROR:/', $line)) {
                    throw new \Exception("Ошибка выполнения SQL файла: \n" . implode("\n", $output) . "\n\n");
                }
            }
        }
    }
}
