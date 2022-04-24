<?php

namespace Letov\Flycatcher\Tests\Spyder;

use DI\DependencyException;
use DI\NotFoundException;
use GearmanClient;
use Letov\Flycatcher\Tests\TestCaseContainer;

class SpyderUrlTemplateTest extends TestCaseContainer
{
    private GearmanClient $client;

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testSpyderUrlTemplate()
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
        $this->container->make('Spyder.urlTemplate', array(
            'downloadDir' => $this->container->get('Directories.paths')['download'],
            'taskLimit' => $this->container->get("Worker.downloadToolWorker.count"),
            'client' => $this->client,
            'cache' => $this->container->get('Cache'),
            'jsonUrlTree' => $this->container->make('JsonUrlTree', array(
                'jsonFilePath' => $this->container->get('Directories.paths')['download'] . "/struct.json"
            )),
            'template' => 'https://some.com/?page=%d',
            'range' => range(1,4),
        ));
        //$this->assertFileExists($this->container->get('Directories.paths')['download'] . "/struct.json");
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    function setWorkers()
    {
        $args = array(
            'CookieFilePath' => $this->tmpCookie,
            'Timeout' => $this->container->get('Downloader.timeout'),
        );
        for ($i = 0; $i < $this->container->get("Worker.downloadToolWorker.count") * 2; $i++)
        {
            $this->client->addTask("setDownloadTool", serialize(array(
                'downloadToolName' => 'Wget',
                'shellName' => 'Wget.shell',
                'args' => $args
            )));
        }
        $this->client->runTasks();
    }
}
