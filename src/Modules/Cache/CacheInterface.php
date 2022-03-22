<?php

namespace Letov\Flycatcher\Modules\Cache;

interface CacheInterface
{
    public function valid(string $filePath): bool;
}