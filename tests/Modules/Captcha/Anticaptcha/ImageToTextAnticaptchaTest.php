<?php

namespace Letov\Flycatcher\Tests\Modules\Captcha\Anticaptcha;

use DI\DependencyException;
use DI\NotFoundException;
use Letov\Flycatcher\Modules\Downloader\ShellCmdControllers\Curl;
use Letov\Flycatcher\Tests\TestCaseIncludeContainer;

class ImageToTextAnticaptchaTest extends TestCaseIncludeContainer
{
    private Curl $curl;

    /**
     * @throws NotFoundException
     * @throws DependencyException
     */
    public function testAnticaptcha()
    {
        $this->curl = $this->container->make('Curl', array('args' =>
            array(
                'HttpMethod' => 'GET',
                'TimeOut' => $this->container->get('Downloader.timeout'),
                'CookieFilePath' => $this->tmpCookie,
                'ShellCmd' => $this->container->get("ShellCmd.curl")
            )
        ));
        $this->curl->downloadFile("http://democaptcha.com/demo-form-eng/image.html", $this->tmpFile);
        $this->getCaptchaImage();
        $this->sendForm();
        $this->assertStringContainsString("Your message has been sent", file_get_contents($this->tmpFile));
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
        $this->curl->updateArg(
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
}
