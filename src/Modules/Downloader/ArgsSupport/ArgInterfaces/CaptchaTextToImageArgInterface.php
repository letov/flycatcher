<?php

namespace Letov\Flycatcher\Modules\Downloader\ArgsSupport\ArgInterfaces;

interface CaptchaTextToImageArgInterface
{
    public function getCaptchaApiKey(): ?string;
    public function getCaptchaSign(): ?string;
    public function getCaptchaImageSelector(): ?string;
    public function getCaptchaInputSelector(): ?string;
    public function getCaptchaFormSelector(): ?string;
}