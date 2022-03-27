<?php

namespace Letov\Flycatcher\Modules\Downloader\ArgsSupport\ArgInterfaces;

interface TimeOutArgInterface
{
    public function getTimeOut(): ?int;
}