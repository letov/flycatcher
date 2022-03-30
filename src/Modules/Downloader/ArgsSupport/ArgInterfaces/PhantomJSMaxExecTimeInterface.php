<?php

namespace Letov\Flycatcher\Modules\Downloader\ArgsSupport\ArgInterfaces;

interface PhantomJSMaxExecTimeInterface
{
    public function getPhantomJSMaxExecTime(): ?int;
}