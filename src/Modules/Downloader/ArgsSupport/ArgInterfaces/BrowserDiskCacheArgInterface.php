<?php

namespace Letov\Flycatcher\Modules\Downloader\ArgsSupport\ArgInterfaces;

interface BrowserDiskCacheArgInterface
{
    public function getDiskCachePath(): ?string;
    public function getLocalStoragePath(): ?string;
}