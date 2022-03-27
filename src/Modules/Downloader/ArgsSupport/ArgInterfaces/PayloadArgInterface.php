<?php

namespace Letov\Flycatcher\Modules\Downloader\ArgsSupport\ArgInterfaces;

interface PayloadArgInterface
{
    public function getPayload(): ?string;
}