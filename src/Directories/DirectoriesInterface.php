<?php

namespace Letov\Flycatcher\Directories;

interface DirectoriesInterface
{
    public function initAppDirs(array $dirPaths);
    public function emptyDirs(array $dirPaths);
}