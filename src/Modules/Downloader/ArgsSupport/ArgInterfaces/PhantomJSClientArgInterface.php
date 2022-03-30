<?php

namespace Letov\Flycatcher\Modules\Downloader\ArgsSupport\ArgInterfaces;

use JonnyW\PhantomJs\ClientInterface;

interface PhantomJSClientArgInterface
{
    public function getPhantomJSClient(): ?ClientInterface;
}