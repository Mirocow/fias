<?php

namespace FileSystem;

class Dearchiver
{
    public static function extract($pathToFileDirectory, $pathToFile)
    {
        $pathToFile = $pathToFileDirectory . '/' . $pathToFile;

        $directory = static::generateDirectoryName($pathToFileDirectory, $pathToFile);
        if(!file_exists($directory)){
            mkdir($directory, 0770);
        }
        static::checkPaths($directory);
        static::doExtract($directory, $pathToFile);

        return $directory;
    }

    private static function checkPaths($pathToFileDirectory)
    {
        FileHelper::ensureIsReadable($pathToFileDirectory);
        FileHelper::ensureIsDirectory($pathToFileDirectory);
        FileHelper::ensureIsWritable($pathToFileDirectory);
    }

    private static function generateDirectoryName($pathToFileDirectory, $pathToFile)
    {


        // Формируем имя папки вида VersionID_DateAndTime
        return $pathToFileDirectory
            . '/'
            . basename($pathToFile, '.rar')
        ;
    }

    private static function doExtract($directoryForExtract, $pathToFile)
    {

        $pathToFile          = escapeshellarg($pathToFile);
        $directoryForExtract = escapeshellarg($directoryForExtract);

        exec('unrar e ' . $pathToFile . ' ' . $directoryForExtract . ' 2>&1', $output, $result);

        if ($result !== 0) {
            throw new \Exception('Ошибка разархивации: ' . implode("\n", $output));
        }

        return $directoryForExtract;
    }
}
