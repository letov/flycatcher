<?php

declare(strict_types=1);

namespace Letov\Flycatcher\Cache;

interface CacheInterface
{
    public function valid(string $filePath): bool;
}
