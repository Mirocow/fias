<?php

namespace Containers;
use Loader\InitLoader;
use Loader\UpdateLoader;

/**
 * Trait ParserContainerTrait
 * @package Container
 */
trait ParserContainerTrait
{

    private $imports = [];

    /**
     * @return string
     */
    public function getDatabaseSourcesDirectory()
    {
        return __DIR__ . '/../../database';
    }

    /**
     * @return mixed|string
     * @throws \Exception
     */
    public function getWsdlUrl()
    {
        return $this->getConfig('app.wsdl_url');
    }

    /**
     * @return mixed|string
     * @throws \Exception
     */
    public function getFileDirectory()
    {
        return $this->getConfig('app.file_directory');
    }

    /**
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    public function getImportConfig($name)
    {
        if(!$this->imports) {
            $configDirectory = $this->getConfigDirectory();
            $this->imports = require($configDirectory . '/import.php');
        }

        if (empty($this->imports[$name])) {
            //throw new Exception("The config by {$name} not found");
            return false;
        }

        return $this->imports[$name];
    }

    /**
     * @return UpdateLoader
     * @throws \Exception
     */
    public function getUpdateLoader()
    {
        return new \Loader\UpdateLoader($this->getWsdlUrl(), $this->getFileDirectory());
    }

    /**
     * @return InitLoader
     * @throws \Exception
     */
    public function getInitLoader()
    {
        $loader = new InitLoader($this->getWsdlUrl(), $this->getFileDirectory(), $this->getConfig('app.upgrade.till'));
        return $loader;
    }

    /**
     * @param $name
     * @return int|mixed
     */
    public function getParserLimit($name)
    {
        $limits = $this->getParserLimits();

        return isset($limits[$name]) ? $limits[$name] : 1000;
    }

    /**
     * @return array
     */
    private function getParserLimits()
    {
        $limits = [];

        foreach ($this->config as $fieldName => $value) {
            if (strpos($fieldName, 'parser.limits') !== false) {
                $fieldName = str_replace('parser.limits.', '', $fieldName);
                $limits[$fieldName] = $value;
            }
        }

        return $limits;
    }
}
