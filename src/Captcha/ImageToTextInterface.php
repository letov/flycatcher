<?php

declare(strict_types=1);

namespace Letov\Flycatcher\Captcha;

interface ImageToTextInterface
{
    public function solve($imageFilePath): string;
}
