<?php

declare(strict_types=1);

namespace Letov\Flycatcher\Tests\Downloader\ToolSupport\Shells;

use DI\DependencyException;
use DI\NotFoundException;
use Letov\Flycatcher\Tests\TestCaseContainer;

/**
 * @internal
 *
 * @coversNothing
 */
final class PhantomJSTest extends TestCaseContainer
{
    /**
     * @throws NotFoundException
     * @throws DependencyException
     */
    public function testPhantomJS(): void
    {
        $phantomJS = $this->container->make('PhantomJS', [
            'argsSupport' => $this->container->make('ArgSupport', [
                'args' => [
                    'Timeout' => $this->container->get('Downloader.timeoutWithCaptcha'),
                    'CookieFilePath' => $this->tmpCookie,
                    'Headers' => [
                        'User-Agent' => 'someUserAgent',
                        'Referer' => 'https://someReferer.com',
                    ],
                    'PayloadDataArray' => [
                        'message' => 'test',
                    ],
                    'CaptchaApiKey' => $this->container->get('Anticaptcha.apiKey'),
                    'CaptchaSign' => 'Try to recognize symbols on the picture',
                    'CaptchaImageSelector' => '#htest_image',
                    'CaptchaInputSelector' => '#vericode',
                    'CaptchaFormSelector' => '#image_captcha_demo_form',
                    'CaptchaSendIncorrectSolveReport' => false,
                    'PhantomJSConnector' => $this->container->get('PhantomJS.connector.path'),
                    'PhantomJSViewportWidth' => 800,
                    'PhantomJSViewportHeight' => 480,
                    'PhantomJSSnapshotPath' => $this->tmpFile.'_snap.png',
                    'PhantomJSSnapshotSelector' => 'body',
                    'Shell' => $this->container->get('PhantomJS.shell'),
                ],
            ]),
            'logger' => $this->container->get('Logger'),
        ]);
        $phantomJS->downloadFile('https://democaptcha.com/demo-form-eng/image.html', $this->tmpFile);
        static::assertFileExists($this->tmpFile);
        // $this->assertStringNotContainsString('Invalid verification code', file_get_contents($this->tmpFile));
    }
}
