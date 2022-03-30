<?php

namespace Letov\Flycatcher\Modules\Downloader;

interface DownloaderInterface
{
    public function downloadFile($url, $filePath);
}