<?php

namespace Loader;

use FileSystem\Directory;

class UpdateLoader extends Base
{
    /**
     * @return Directory
     */
    public function load()
    {
        $filesInfo = $this->getLastFileInfo();

        if(floatval($filesInfo->getVersionId()) < floatval($this->updateTillVersion)){
            throw new \Exception("Import fias data not found");
        }

        return $this->wrap(
            $this->loadFile($filesInfo->getInitFileName(), $filesInfo->getInitFileUrl())
        );
    }
}
