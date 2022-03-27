<?php

namespace Letov\Flycatcher\Modules\Downloader\ArgsSupport\ArgInterfaces;

interface CookieArgInterface
{
    public function getCookieFilePath(): ?string;
}