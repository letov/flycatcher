<?php

namespace Letov\Flycatcher\Downloader\ArgsSupport\ArgInterfaces;

interface HTTPArgInterface
{
    public function getHeaders(): ?array;

    public function getHttpMethod(): ?string;

    public function getPayloadDataArray(): ?array;

    public function getPayloadDataRaw(): ?string;

    public function getPayloadDataFormArray(): ?string;

    public function getPayloadDataFormRaw(): ?string;
}