<?php

namespace Letov\Flycatcher\Modules\Downloader\ArgsSupport\ArgInterfaces;

interface PhantomJSCaptchaSignInterface
{
    public function getPhantomJSCaptchaSign(): ?string;
}