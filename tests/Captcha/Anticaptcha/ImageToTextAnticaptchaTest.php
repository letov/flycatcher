<?php

namespace Letov\Flycatcher\Tests\Captcha\Anticaptcha;

use DI\DependencyException;
use DI\NotFoundException;
use Letov\Flycatcher\Downloader\ToolSupport\Shells\Curl;
use Letov\Flycatcher\Tests\TestCaseContainer;

class ImageToTextAnticaptchaTest extends TestCaseContainer
{
    private Curl $curl;

    /**
     * @throws NotFoundException
     * @throws DependencyException
     */
    public function testAnticaptcha()
    {
        $argsSupport = $this->container->make('ArgSupport', array('args' =>
            array(
                'HttpMethod' => 'GET',
                'Timeout' => $this->container->get('Downloader.timeout'),
                'CookieFilePath' => $this->tmpCookie,
                'Shell' => $this->container->get("Curl.shell")
            )
        ));
        $this->curl = $this->container->make('Curl', array(
            'argsSupport' => $argsSupport,
            'logger' => $this->container->get('Logger')
        ));
        $this->curl->downloadFile("http://democaptcha.com/demo-form-eng/image.html", $this->tmpFile);
        $this->getCaptchaImage();
        $this->sendForm();
        //$this->assertStringContainsString("Your message has been sent", file_get_contents($this->tmpFile));
    }

    /**
     * @throws NotFoundException
     * @throws DependencyException
     */
    function getCaptchaImage()
    {
        $this->curl->downloadFile(
            $this->container
                ->get('DomParser')
                ->loadFromFile($this->tmpFile)
                ->find('#htest_image')[0]
                ->getAttribute('src'),
            $this->tmpFile);
        $old = $this->tmpFile;
        $this->tmpFile .= '.jpg';
        rename($old, $this->tmpFile);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    function sendForm()
    {
        $captchaText = $this->container
            ->get('Captcha.imageToText')
            ->solve($this->tmpFile);
        $this->curl->updateArgs(
            array(
                'HttpMethod' => 'POST',
                'Headers' => array(
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ),
                'PayloadForm' => array(
                    'message' => 'hello world',
                    'vericode' => $captchaText,
                    'formid' => 'image_captcha_demo_form',
                ),
            )
        );
        $this->curl->downloadFile("http://democaptcha.com/demo-form-eng/image.html", $this->tmpFile);
    }
}
