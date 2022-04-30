<?php

namespace Letov\Flycatcher\Downloader\ArgsSupport\ArgInterfaces;

interface SeleniumArgInterface
{
    public function getOffHeadlessMode(): ?bool;

    public function getBeforeDownloadCall(): ?string;
}