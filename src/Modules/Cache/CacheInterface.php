<?php

namespace Letov\Flycatcher\Modules\Cache;

interface CacheInterface
{
    public function __construct(int $maxFileLifetimeSecond, bool $imageAlwaysFresh);
    public function valid(string $filePath): bool;
}