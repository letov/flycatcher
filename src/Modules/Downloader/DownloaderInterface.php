<?php

namespace Letov\Flycatcher\Modules\Downloader;

use Letov\Flycatcher\Modules\Proxy\ProxyInterface;

interface DownloaderInterface
{
    public function downloadFile($url, $filePath);
}