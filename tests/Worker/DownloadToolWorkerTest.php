<?php

namespace Letov\Flycatcher\Tests\Worker;

use DI\DependencyException;
use DI\NotFoundException as NotFoundExceptionAlias;
use Exception;
use Letov\Flycatcher\Tests\TestCaseContainer;

class DownloadToolWorkerTest extends TestCaseContainer
{
    protected array $workerPid;
    protected int $workerCount;

    /**
     * @throws DependencyException
     * @throws NotFoundExceptionAlias
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->workerCount = 5;
        $args = array(
            'TimeOut' => $this->container->get('Downloader.timeoutWithCaptcha'),
            'Headers' => array(
                'User-Agent' => 'someUserAgent',
                'Referer' => 'https://someReferer.com',
            ),
            'PayloadForm' => array(
                'message' => 'test',
            ),
            'CaptchaApiKey' => $this->container->get("Anticaptcha.apiKey"),
            'CaptchaSign' => 'Try to recognize symbols on the picture',
            'CaptchaImageSelector' => '#htest_image',
            'CaptchaInputSelector' => '#vericode',
            'CaptchaFormSelector' => '#image_captcha_demo_form',
            'CaptchaSendIncorrectSolveReport' => false,
            'PhantomJSConnector' => $this->container->get('PhantomJS.connector.captchaImageToText'),
            'PhantomJSViewportWidth' => 800,
            'PhantomJSViewportHeight' => 480,
            'PhantomJSSnapshotSelector' => 'body',
            'Shell' => $this->container->get("PhantomJS.shell")
        );
        for ($i = 0; $i < $this->workerCount; $i++)
        {
            $pid = pcntl_fork();
            if (-1 == $pid)
            {
                throw new Exception('Cant create test worker process');
            } elseif ($pid) {
                $this->workerPid[] = $pid;
            } else {
                $args['PhantomJSSnapshotPath'] = $this->tmpFile . "_snap_" . $i . ".png";
                $args['CookieFilePath'] = $this->tmpCookie . "_" . $i;
                $worker = $this->container->make('DownloadToolWorker', array(
                    'downloadTool' => $this->container->make('PhantomJS', array(
                        'argsSupport' => $this->container->make('ArgSupport', array(
                            'args' => $args
                        ))
                    ))
                ));
                $worker->work();
                exit;
            }
        }
    }

    protected function tearDown(): void
    {
        foreach ($this->workerPid as $pid)
        {
            shell_exec("kill -9 $pid");
        }
        parent::tearDown();
    }

    function testWorker()
    {
        $client = $this->container->get('GearmanClient');
        $client->addServer();
        $client->setCompleteCallback(function($task)
        {
            echo "COMPLETE SOME WORKER TASK\n";
        });
        $client->setTimeout($this->container->get("Downloader.timeoutWithCaptcha") * 1000);
        for ($i = 0; $i < $this->workerCount; $i++) {
            $client->addTask("download", serialize(array(
                'url' => 'http://democaptcha.com/demo-form-eng/image.html',
                'filePath' => $this->tmpFile . "_res_" . $i,
            )));
        }
        $client->runTasks();
        for ($i = 0; $i < $this->workerCount; $i++) {
            $filePath = $this->tmpFile . "_res_" . $i;
            $this->assertStringContainsString("Your message has been sent", file_get_contents($filePath));
        }
    }
}
