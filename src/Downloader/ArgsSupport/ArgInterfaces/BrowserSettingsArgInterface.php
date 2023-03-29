<?php

declare(strict_types=1);

namespace Letov\Flycatcher\Downloader\ArgsSupport\ArgInterfaces;

use Letov\Flycatcher\ProxyPool\ProxyInterface;

interface BrowserSettingsArgInterface
{
    public function getProxy(): ?ProxyInterface;

    public function getTimeout(): ?int;

    public function getCookieFilePath(): ?string;

    public function getDiskCachePath(): ?string;

    public function getLocalStoragePath(): ?string;
}
