<?php

namespace Fias;

class Config
{
    private $config = array();

    protected function __construct($pathToFile)
    {
        /** @noinspection PhpIncludeInspection */
        $this->config = require $pathToFile;
        if (!is_array($this->config)) {
            throw new \LogicException('Ошибка загрузки конфигурационного файла: ' . $pathToFile);
        }
    }

    public function getParam($key)
    {
        if (!isset($this->config[$key])) {
            throw new ConfigException('Не найден конфигурационный параметр: ' . $key);
        }

        return $this->config[$key];
    }

    /** @var Config[] */
    private static $configCaches = array();

    public static function get($name)
    {
        $name       = basename($name);
        $pathToFile = __DIR__ . '/../../config/' . $name . '.php';

        if (!is_file($pathToFile)) {
            throw new FileException('Файл не найден: ' . $pathToFile);
        }

        if (!isset(static::$configCaches[$pathToFile])) {
            static::$configCaches[$pathToFile] = new Config($pathToFile);
        }

        return static::$configCaches[$pathToFile];
    }
}