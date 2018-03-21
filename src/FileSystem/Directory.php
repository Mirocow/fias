<?php

namespace FileSystem;

class Directory
{
    private $directoryPath;

    public function __construct($path)
    {
        FileHelper::ensureIsReadable($path);
        FileHelper::ensureIsDirectory($path);

        $this->directoryPath = $path;
    }

    public function getVersionId()
    {
        return str_replace('VERSION_ID_', '', $this->findFile('VERSION_ID_'));
    }

    /**
     * @return null|string
     * @throws FileException
     */
    public function getDeletedAddressObjectFile()
    {
        $fileName = $this->findFile('AS_DEL_ADDROBJ_', false);

        return $fileName ? $this->directoryPath . '/' . $fileName : null;
    }

    /**
     * @return null|string
     * @throws FileException
     */
    public function getDeletedHouseFile()
    {
        $fileName = $this->findFile('AS_DEL_HOUSE_', false);

        return $fileName ? $this->directoryPath . '/' . $fileName : null;
    }

    /**
     * File like AS_ADDROBJ_20180208_06eadc49-ad5a-42f2-8cee-d3f77075d5fb.XML
     * @return string
     * @throws FileException
     */
    public function getAddressObjectFile()
    {
        return $this->directoryPath . '/' . $this->findFile('AS_ADDROBJ_');
    }

    /**
     * File like AS_HOUSE_20180208_0c559e63-0910-4db8-8885-6a175cb400f4.XML
     * @return string
     * @throws FileException
     */
    public function getHousesFile()
    {
        return $this->directoryPath . '/' . $this->findFile('AS_HOUSE_');
    }

    /**
     * File like AS_ROOM_20180208_0503df34-6cfd-457a-b8a6-3c2c7b6be306.XML
     * @return string
     * @throws FileException
     */
    public function getRoomsFile()
    {
        return $this->directoryPath . '/' . $this->findFile('AS_ROOM_');
    }

    public function getPath()
    {
        return $this->directoryPath;
    }

    /**
     * @param $prefix
     * @param bool $isIndispensable
     * @return null
     * @throws FileException
     */
    private function findFile($prefix, $isIndispensable = true)
    {
        $files = scandir($this->directoryPath);
        foreach ($files as $file) {
            if (in_array($file, ['.', '..'])) {
                continue;
            }

            if (mb_strpos($file, $prefix) === 0) {
                return $file;
            }
        }

        if ($isIndispensable) {
            throw new FileException('Файл с префиксом ' . $prefix . ' не найден в директории: ' . $this->directoryPath);
        }

        return null;
    }
}
