<?php

namespace Loader;

use FileSystem\Directory;

class InitLoader extends Base
{

    /**
     * @return Directory
     * @throws \Exception
     */
    public function load()
    {
        $filesInfo = $this->getLastFileInfo();

        if(floatval($filesInfo->getVersionId()) <= floatval($this->updateTillVersion)){
            $file = $this->loadFile($filesInfo->getInitFileName(), $filesInfo->getInitFileUrl());
        } else {
            $file = $this->fileDirectory . '/' . $this->updateTillVersion . '_fias_xml';
        }

        return $this->wrap($file);
    }
}
