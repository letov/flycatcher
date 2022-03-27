<?php

namespace Letov\Flycatcher\Modules\Downloader\ArgsSupport\ArgInterfaces;

interface HttpMethodArgInterface
{
    public function getHttpMethod(): ?string;
}