<?php

declare(strict_types=1);

namespace Letov\Flycatcher\Cache;

use Letov\Flycatcher\Shell\ShellInterface;

class Cache implements CacheInterface
{
    private int $maxFileLifetimeSecond;
    private bool $imageAlwaysFresh;
    private ShellInterface $stat;

    public function __construct(int $maxFileLifetimeSecond, bool $imageAlwaysFresh, ShellInterface $stat)
    {
        $this->maxFileLifetimeSecond = $maxFileLifetimeSecond;
        $this->imageAlwaysFresh = $imageAlwaysFresh;
        $this->stat = $stat;
    }

    public function valid(string $filePath): bool
    {
        if (!file_exists($filePath)) {
            return false;
        }
        if ($this->isZeroSize($filePath)) {
            @unlink($filePath);

            return false;
        }
        if ($this->imageAlwaysFresh && $this->isImageFile($filePath)) {
            return true;
        }
        if ($this->isFileExpire($filePath)) {
            @unlink($filePath);

            return false;
        }

        return true;
    }

    private function isZeroSize(string $filePath): bool
    {
        $result = 0 === (int) $this->stat
            ->addArg($filePath)
            ->run();
        $this->stat->removeFromTail(1);

        return $result;
    }

    private function isImageFile(string $filePath): bool
    {
        return @\is_array(getimagesize($filePath));
    }

    private function isFileExpire($filePath): bool
    {
        return (time() - fileatime($filePath)) > $this->maxFileLifetimeSecond;
    }
}
