<?php

declare(strict_types=1);

namespace Letov\Flycatcher\Downloader;

interface BrowserEmulationInterface extends DownloadToolInterface
{
    public function makeAction(callable $function);

    public function closeBrowser();
}
