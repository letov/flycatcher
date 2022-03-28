<?php

namespace Letov\Flycatcher\Tests\Modules\Captcha\Anticaptcha;

use DI\DependencyException;
use DI\NotFoundException;
use Letov\Flycatcher\Tests\TestCaseIncludeContainer;

class ImageToTextAnticaptchaTest extends TestCaseIncludeContainer
{
    /**
     * @throws NotFoundException
     * @throws DependencyException
     */
    public function testAnticaptcha()
    {
        $url = "http://democaptcha.com/demo-form-eng/image.html";
        $curlGetArgs = array(
            'HttpMethod' => 'GET',
            'TimeOut' => $this->container->get('Downloader.timeout'),
            'CookieFilePath' => $this->tmpCookie,
        );
        $this->getCaptchaImage($url, $curlGetArgs);
        $this->sendForm($url, $curlGetArgs);
        $this->assertStringContainsString("Your message has been sent", file_get_contents($this->tmpFile));
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    function sendForm($url, $curlGetArgs)
    {
        $captchaText = $this->getCaptchaText();
        $curlGetArgs = array_merge($curlGetArgs, array(
            'HttpMethod' => 'POST',
            'Headers' => array(
                'Content-Type' => 'application/x-www-form-urlencoded',
            ),
            'Payload' => http_build_query(array(
                'message' => 'hello world',
                'vericode' => $captchaText,
                'formid' => 'image_captcha_demo_form',
            )),
        ));
        $this->curlRequest($url, $curlGetArgs);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    function getCaptchaText(): string
    {
        return $this->container
            ->get('Captcha.imageToText')
            ->solve($this->tmpFile);
    }

    /**
     * @throws NotFoundException
     * @throws DependencyException
     */
    function getCaptchaImage($url, $curlArgs)
    {
        $this->curlRequest($url, $curlArgs);
        $captchaLink = $this->container
            ->get('DomParser')
            ->loadFromFile($this->tmpFile)
            ->find('#htest_image')[0]
            ->getAttribute('src');
        $this->curlRequest($captchaLink, $curlArgs);
        $old = $this->tmpFile;
        $this->tmpFile .= '.jpg';
        rename($old, $this->tmpFile);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    function curlRequest($url, $curlArgs)
    {
        $this->container
            ->make('Curl', array(
                'args' => $curlArgs,
                'shellCmd' => $this->container->get("ShellCmd.curl"))
            )
            ->downloadFile($url, $this->tmpFile);
    }
}
