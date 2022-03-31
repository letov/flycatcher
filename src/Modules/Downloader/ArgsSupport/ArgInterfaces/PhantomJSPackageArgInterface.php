<?php

namespace Letov\Flycatcher\Modules\Downloader\ArgsSupport\ArgInterfaces;

use JonnyW\PhantomJs\ClientInterface;

interface PhantomJSPackageArgInterface
{
    public function getPhantomJSClient(): ?ClientInterface;
    public function getPhantomJSPath(): ?string;
}