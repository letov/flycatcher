<?php

namespace Letov\Flycatcher\Modules\Downloader;

interface DownloadInterface
{
    public function downloadFile($url, $filePath);
}