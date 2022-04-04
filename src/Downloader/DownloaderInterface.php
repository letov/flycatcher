<?php

namespace Letov\Flycatcher\Downloader;

interface DownloaderInterface
{
    public function downloadFile($url, $filePath);
}