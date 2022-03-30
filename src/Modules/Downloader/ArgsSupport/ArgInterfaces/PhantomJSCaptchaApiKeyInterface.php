<?php

namespace Letov\Flycatcher\Modules\Downloader\ArgsSupport\ArgInterfaces;

interface PhantomJSCaptchaApiKeyInterface
{
    public function getPhantomJSCaptchaApiKey(): ?string;
}