<?php

namespace Letov\Flycatcher\Downloader\ArgsSupport\ArgInterfaces;

interface HTTPArgInterface
{
    public function getHeaders(): ?array;

    public function getHttpMethod(): ?string;

    public function getPayloadForm(): ?array;

    public function getPayloadRaw(): ?string;
}