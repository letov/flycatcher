<?php

namespace Letov\Flycatcher\Cache;

interface CacheInterface
{
    public function valid(string $filePath): bool;
    public function emptyDirs(array $dirs);
}