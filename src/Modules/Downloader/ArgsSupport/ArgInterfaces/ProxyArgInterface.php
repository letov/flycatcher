<?php

namespace Letov\Flycatcher\Modules\Downloader\ArgsSupport\ArgInterfaces;

use Letov\Flycatcher\Modules\Proxy\ProxyInterface;

interface ProxyArgInterface
{
    public function getProxy(): ?ProxyInterface;
}

