<?php

namespace Letov\Flycatcher\Tests\WorkerPool;

use DI\DependencyException;
use DI\NotFoundException;
use Letov\Flycatcher\Tests\TestCaseContainer;
use Letov\Flycatcher\WorkerPool\WorkerPoolInterface;

class WorkerPoolTest extends TestCaseContainer
{
    protected WorkerPoolInterface $workerPool;
    protected int $workerCount;
    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    function testWorkerPool()
    {
        $this->workerCount = 2;
        $this->createPhantomWorkerPool();
        $client = $this->container->get('Gearman.client');
        $client->addServer(
            $this->container->get('Gearman.host'),
            $this->container->get('Gearman.port')
        );
        $client->setCompleteCallback(function()
        {
            echo "COMPLETE SOME WORKER TASK\n";
        });
        $client->setTimeout($this->container->get("Downloader.timeoutWithCaptcha") * 1000 * 2);
        for ($i = 0; $i < $this->workerCount; $i++)
        {
            $client->addTask("download", serialize(array(
                'url' => 'http://democaptcha.com/demo-form-eng/image.html',
                'filePath' => $this->tmpFile . "_res_" . $i,
            )));
        }
        $client->runTasks();
        for ($i = 0; $i < $this->workerCount; $i++)
        {
            $filePath = $this->tmpFile . "_res_" . $i;
            $this->assertStringContainsString("Your message has been sent", file_get_contents($filePath));
        }
        $this->workerPool->stop();
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    protected function createPhantomWorkerPool()
    {
        $args = array(
            'CookieFilePath' => $this->tmpCookie,
            'Timeout' => $this->container->get('Downloader.timeoutWithCaptcha'),
            'Headers' => array(
                'User-Agent' => 'someUserAgent',
                'Referer' => 'https://someReferer.com',
            ),
            'PayloadForm' => array(
                'message' => 'test',
            ),
            'DiskCachePath' => $this->container->get("Dirs")['browsersData'],
            'LocalStoragePath' => $this->container->get("Dirs")['browsersData'],
            'CaptchaApiKey' => $this->container->get("Anticaptcha.apiKey"),
            'CaptchaSign' => 'Try to recognize symbols on the picture',
            'CaptchaImageSelector' => '#htest_image',
            'CaptchaInputSelector' => '#vericode',
            'CaptchaFormSelector' => '#image_captcha_demo_form',
            'CaptchaSendIncorrectSolveReport' => false,
            'PhantomJSConnector' => $this->container->get('PhantomJS.connector.captchaImageToText'),
            'PhantomJSSnapshotSelector' => 'body',
            'PhantomJSSnapshotPath' => $this->tmpFile . "_snap.png",
            'Shell' => $this->container->get("PhantomJS.shell")
        );
        $this->workerPool = $this->container->make("Worker.pool", array(
            'container' => $this->container,
            'workerName' => 'downloadToolWorker',
            'workerDownloadToolName' => 'PhantomJS',
            'workerArgs' => $args,
            'workerCount' => $this->workerCount + 5,
            'workerCountCheckDelay' => $this->container->get('Worker.countCheckDelay'),
        ));
    }
}
