<?php

namespace Loader;

use FileSystem\Dearchiver;
use \FileSystem\FileHelper;
use \FileSystem\Directory;

/**
 * Class Base
 * @package Loader
 */
abstract class Base
{
    /** @return Directory */
    abstract public function load();

    protected $wsdlUrl;
    protected $fileDirectory;
    protected $updateTillVersion = null;

    /**
     * Base constructor.
     * @param $wsdlUrl
     * @param $fileDirectory
     * @param null $version
     * @throws \FileSystem\FileException
     */
    public function __construct($wsdlUrl, $fileDirectory, $version = null)
    {
        $this->wsdlUrl       = $wsdlUrl;
        $this->fileDirectory = $fileDirectory;

        if($version){
            $this->updateTillVersion = $version;
        }

        FileHelper::ensureIsDirectory($fileDirectory);
        FileHelper::ensureIsWritable($fileDirectory);
    }

    /** @var SoapResultWrapper */
    private $fileInfoResult = null;

    /**
     * @return SoapResultWrapper
     */
    public function getLastFileInfo()
    {
        if (!$this->fileInfoResult) {
            $this->fileInfoResult = $this->getLastFileInfoRaw();
        }

        return $this->fileInfoResult;
    }

    /**
     * @return SoapResultWrapper
     */
    private function getLastFileInfoRaw()
    {
        $client    = new \SoapClient($this->wsdlUrl);
        $rawResult = $client->__soapCall('GetLastDownloadFileInfo', []);

        return new SoapResultWrapper($rawResult);
    }

    /**
     * @param $fileName
     * @param $url
     * @return string
     * @throws \Exception
     */
    protected function loadFile($fileName, $url)
    {
        $filePath = $this->fileDirectory . '/' . $fileName;
        if (file_exists($filePath)) {
            if ($this->isFileSizeCorrect($filePath, $url)) {
                return $filePath;
            }

            unlink($filePath);
        }

        $fp = fopen($filePath, 'w');
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_HEADER, false );
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        curl_exec($ch);

        //If there was an error, throw an Exception
        if (curl_errno($ch)) {
            throw new \Exception(curl_error($ch));
        }

        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);
        fclose($fp);

        if($statusCode == 200){
            return $filePath;
        } else{
            throw new \Exception("File was not dowload");
        }
    }

    /**
     * @param $path
     * @return Directory
     */
    protected function wrap($path)
    {
        $pathinfo = pathinfo($path);

        // Если нет распакованной директории
        if(!file_exists($this->fileDirectory . '/' . $pathinfo['filename'])) {
            $pathToDirectory = Dearchiver::extract($this->fileDirectory, $pathinfo['basename']);
        } else {
            $pathToDirectory = $this->fileDirectory . '/' . $pathinfo['filename'];
        }

        $this->addVersionId($pathToDirectory);

        return new Directory($pathToDirectory);
    }

    /**
     * @param $pathToDirectory
     */
    private function addVersionId($pathToDirectory)
    {
        $versionId = $this->getLastFileInfo()->getVersionId();

        file_put_contents($pathToDirectory . '/VERSION_ID_' . $versionId, 'Версия: ' . $versionId);
    }

    /**
     * @param $filePath
     * @param $url
     * @return bool
     */
    public function isFileSizeCorrect($filePath, $url)
    {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);

        curl_exec($ch);

        $correctSize = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);

        curl_close($ch);

        return (filesize($filePath) == $correctSize);
    }
}
