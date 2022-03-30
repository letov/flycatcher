<?php

namespace Letov\Flycatcher\Modules\Downloader;

interface DownloadSupportInterface
{
    public function downloadFile($url, $filePath);
}