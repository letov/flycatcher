<?php

namespace Letov\Flycatcher\Modules\Captcha;

interface ImageToTextInterface
{
    public function solve($imageFilePath): string;
}