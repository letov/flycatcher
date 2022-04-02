<?php

namespace Letov\Flycatcher\Modules\Downloader\ArgsSupport\ArgInterfaces;

use Letov\Flycatcher\Modules\ProxyPool\ProxyInterface;

interface HttpBaseArgInterface
{
    public function getCookieFilePath(): ?string;
    public function getHeaders(): ?array;
    public function getHttpMethod(): ?string;
    public function getPayloadForm(): ?array;
    public function getPayloadRaw(): ?string;
    public function getProxy(): ?ProxyInterface;
    public function getTimeOut(): ?int;
}