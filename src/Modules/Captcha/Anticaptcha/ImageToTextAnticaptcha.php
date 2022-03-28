<?php

namespace Letov\Flycatcher\Modules\Captcha\Anticaptcha;

use Exception;
use ImageToText;
use Letov\Flycatcher\Modules\Captcha\ImageToTextInterface;

class ImageToTextAnticaptcha implements ImageToTextInterface
{

    private ImageToText $api;

    public function __construct(string $apiKey)
    {
        $this->api = new ImageToText();
        $this->api->setKey($apiKey);
    }

    /**
     * @throws Exception
     */
    public function solve($imageFilePath): string
    {
        $this->api->setFile($imageFilePath);
        if (!$this->api->createTask()) {
            throw new Exception("Anticaptcha task fail");
        }
        if (!$this->api->waitForResult()) {
            throw new Exception($this->api->getErrorMessage());
        }
        return $this->api->getTaskSolution();
    }
}