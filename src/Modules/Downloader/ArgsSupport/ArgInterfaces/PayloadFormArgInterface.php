<?php

namespace Letov\Flycatcher\Modules\Downloader\ArgsSupport\ArgInterfaces;

interface PayloadFormArgInterface
{
    public function getPayloadForm(): ?array;
}