<?php

namespace Letov\Flycatcher\Captcha;

interface ImageToTextInterface
{
    public function solve($imageFilePath): string;
}