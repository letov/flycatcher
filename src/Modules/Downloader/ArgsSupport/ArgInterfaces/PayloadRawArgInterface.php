<?php

namespace Letov\Flycatcher\Modules\Downloader\ArgsSupport\ArgInterfaces;

interface PayloadRawArgInterface
{
    public function getPayloadRaw(): ?string;
}