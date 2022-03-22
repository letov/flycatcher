<?php

namespace Letov\Flycatcher\Modules\Cache;

use Letov\Flycatcher\Modules\ShellCmd\ShellCmdInterface;

class Cache implements CacheInterface
{
    private int $maxFileLifetimeSecond;
    private bool $imageAlwaysFresh;
    private ShellCmdInterface $stat;

    public function __construct(int $maxFileLifetimeSecond, bool $imageAlwaysFresh, ShellCmdInterface $stat)
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

    private function isFileExpire($filePath): bool
    {
        return (time() - filemtime($filePath)) > $this->maxFileLifetimeSecond;
    }

    private function isZeroSize(string $filePath): bool
    {
        return 0 == (int)$this->stat
                    ->addArg($filePath)
                    ->run();
    }

    private function isImageFile(string $filePath): bool
    {
        return @is_array(getimagesize($filePath));
    }
}