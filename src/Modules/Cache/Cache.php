<?php

namespace Letov\Flycatcher\Modules\Cache;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Letov\Flycatcher\Modules\ShellCmd\ShellCmd;

class Cache implements CacheInterface
{
    private int $maxFileLifetimeSecond;
    private bool $imageAlwaysFresh;
    private Container $container;

    public function __construct(int $maxFileLifetimeSecond, bool $imageAlwaysFresh)
    {
        $this->maxFileLifetimeSecond = $maxFileLifetimeSecond;
        $this->imageAlwaysFresh = $imageAlwaysFresh;
        $this->container = new Container();
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
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

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    private function isZeroSize(string $filePath): bool
    {
        return 0 == (int)$this->container->get(ShellCmd::class)
                    ->addArg("--printf", "%s", "=")
                    ->addFlag($filePath)
                    ->run("stat");
    }

    private function isImageFile(string $filePath): bool
    {
        return @is_array(getimagesize($filePath));
    }
}