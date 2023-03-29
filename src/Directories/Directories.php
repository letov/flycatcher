<?php

declare(strict_types=1);

namespace Letov\Flycatcher\Directories;

class Directories implements DirectoriesInterface
{
    public function initAppDirs(array $dirPaths): void
    {
        foreach ($dirPaths as $dirPath) {
            $this->createDirIfNotExist($dirPath);
        }
    }

    public function emptyDirs(array $dirPaths): void
    {
        foreach ($dirPaths as $dirPath) {
            shell_exec("rm -rf {$dirPath}/*");
        }
    }

    private function createDirIfNotExist($dirPath): void
    {
        if (!file_exists($dirPath)) {
            mkdir($dirPath);
        }
    }
}
