<?php

namespace Letov\Flycatcher\Tests\Modules\Downloader\Shells;

use DI\DependencyException;
use DI\NotFoundException;
use Letov\Flycatcher\Tests\TestCaseIncludeContainer;

class PhantomJSTest extends TestCaseIncludeContainer
{
    /**
     * @throws NotFoundException
     * @throws DependencyException
     */
    public function testPhantomJS()
    {
        $phantomJS = $this->container->make('PhantomJS', array(
            'argsSupport' => $this->container->make('ArgSupport', array(
                'args' =>  array(
                    'TimeOut' => $this->container->get('Downloader.timeoutWithCaptcha'),
                    'CookieFilePath' => $this->tmpCookie,
                    'Headers' => array(
                        'User-Agent' => 'someUserAgent',
                        'Referer' => 'https://someReferer.com',
                    ),
                    'CaptchaApiKey' => $this->container->get("Anticaptcha.apiKey"),
                    'CaptchaSign' => 'Try to recognize symbols on the picture',
                    'CaptchaImageSelector' => '#htest_image',
                    'CaptchaInputSelector' => '#vericode',
                    'CaptchaFormSelector' => '#image_captcha_demo_form',
                    'PhantomJSConnector' => $this->container->get('PhantomJS.connector.captchaImageToText'),
                    'PhantomJSViewportWidth' => 800,
                    'PhantomJSViewportHeight' => 480,
                    'Shell' => $this->container->get("Shell.phantomJS")
                )
            ))
        ));
        $phantomJS->downloadFile("http://democaptcha.com/demo-form-eng/image.html", $this->tmpFile);
        $this->assertFileExists($this->tmpFile);
        $this->assertStringNotContainsString('Invalid verification code', file_get_contents($this->tmpFile));
    }
}
