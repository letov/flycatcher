<?php

namespace Letov\Flycatcher\Downloader;

interface BrowserEmulationInterface extends DownloadToolInterface
{
    public function makeAction(callable $function);
    public function closeBrowser();
}