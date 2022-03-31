<?php

namespace Letov\Flycatcher\Modules\Downloader\ArgsSupport\ArgInterfaces;

interface PhantomJSArgInterface
{
    public function getPhantomJSConnector(): ?string;
    public function getPhantomJSViewportWidth(): ?int;
    public function getPhantomJSViewportHeight(): ?int;
}