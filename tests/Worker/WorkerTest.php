<?php

namespace Letov\Flycatcher\Tests\Worker;

use DI\DependencyException;
use DI\NotFoundException;
use GearmanClient;
use Letov\Flycatcher\Tests\TestCaseContainer;

class WorkerTest extends TestCaseContainer
{
    private GearmanClient $client;

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    function testWorker()
    {
        $this->client = $this->container->get('Gearman.client');
        $this->client->addServer(
            $this->container->get('Gearman.host'),
            $this->container->get('Gearman.port')
        );
        $this->client->setCompleteCallback(function ($task) {
            echo "complete task {$task->jobHandle()} {$task->functionName()}\n";
        });
        $this->client->setTimeout($this->container->get("Downloader.timeoutWithCaptcha") * 1000 * 2);
        $this->setWorkers();
        for ($i = 0; $i < $this->container->get("Worker.downloadToolWorker.count"); $i++) {
            $this->client->addTask("download", serialize(array(
                'url' => 'http://democaptcha.com/demo-form-eng/image.html',
                'filePath' => $this->tmpFile . "_res_" . $i,
            )));
        }
        $this->client->runTasks();
        for ($i = 0; $i < 5; $i++) {
            $filePath = $this->tmpFile . "_res_" . $i;
            //$this->assertStringContainsString("Your message has been sent", file_get_contents($filePath));
        }
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    function setWorkers()
    {
        $args = array(
            'CookieFilePath' => $this->tmpCookie,
            'Timeout' => $this->container->get('Downloader.timeoutWithCaptcha'),
            'PayloadForm' => array(
                'message' => 'test',
            ),
            'DiskCachePath' => $this->container->get('Directories.paths')['browsersData'],
            'LocalStoragePath' => $this->container->get('Directories.paths')['browsersData'],
            'CaptchaApiKey' => $this->container->get("Anticaptcha.apiKey"),
            'CaptchaSign' => 'Try to recognize symbols on the picture',
            'CaptchaImageSelector' => '#htest_image',
            'CaptchaInputSelector' => '#vericode',
            'CaptchaFormSelector' => '#image_captcha_demo_form',
            'CaptchaSendIncorrectSolveReport' => false,
            'PhantomJSConnector' => $this->container->get('PhantomJS.connector.path'),
            'PhantomJSSnapshotSelector' => 'body',
            'PhantomJSSnapshotPath' => $this->tmpFile . "_snap.png",
        );
        for ($i = 0; $i < $this->container->get("Worker.downloadToolWorker.count"); $i++)
        {
            $this->client->addTask("setDownloadTool", serialize(array(
                'downloadToolName' => 'PhantomJS',
                'shellName' => 'PhantomJS.shell',
                'args' => $args
            )));
        }
        $this->client->runTasks();
    }
}
