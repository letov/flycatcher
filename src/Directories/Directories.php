<?php

namespace Letov\Flycatcher\Directories;

class Directories implements DirectoriesInterface
{

    public function initAppDirs(array $dirPaths)
    {
        foreach ($dirPaths as $dirPath)
        {
            $this->createDirIfNotExist($dirPath);
        }
    }

    public function emptyDirs(array $dirPaths)
    {
        foreach ($dirPaths as $dirPath)
        {
            shell_exec("rm -rf $dirPath/*");
        }
    }

    private function createDirIfNotExist($dirPath)
    {
        if (!file_exists($dirPath)) {
            mkdir($dirPath);
        }
    }
}