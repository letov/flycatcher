<?php

namespace Letov\Flycatcher\Modules\Downloader\ArgsSupport\ArgInterfaces;

interface PhantomJSConnectorInterface
{
    public function getPhantomJSConnector(): ?string;
}