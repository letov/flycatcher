<?php

namespace Letov\Flycatcher\Downloader\ArgsSupport\ArgInterfaces;

interface SeleniumArgInterface
{
    public function getOffHeadlessMode(): ?bool;
}