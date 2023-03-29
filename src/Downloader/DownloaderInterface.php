<?php

declare(strict_types=1);

namespace Letov\Flycatcher\Downloader;

interface DownloaderInterface
{
    public function downloadFile($url, $filePath);
}
