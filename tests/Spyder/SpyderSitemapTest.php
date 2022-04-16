<?php

namespace Letov\Flycatcher\Tests\Spyder;

use DI\DependencyException;
use DI\NotFoundException;
use GearmanClient;
use Letov\Flycatcher\Tests\TestCaseContainer;

class SpyderSitemapTest extends TestCaseContainer
{
    private GearmanClient $client;

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testSpyderSitemap()
    {
        $this->client = $this->container->get('Gearman.client');
        $this->client->addServer(
            $this->container->get('Gearman.host'),
            $this->container->get('Gearman.port')
        );
        $this->client->setCompleteCallback(function ($task) {
            echo sprintf("complete task %s %s\n", $task->jobHandle(), $task->functionName());
        });
        $this->client->setTimeout($this->container->get("Downloader.timeoutWithCaptcha") * 1000 * 2);
        $this->setWorkers();
        /*$this->container->make("SpyderSitemap", array(
            'host' => 'lastprint.ru',
            'protocol' => 'https',
            'downloadDir' => $this->container->get('Dirs')['tests'],
            'taskLimit' => $this->container->get("Worker.downloadToolWorker.count"),
            'client' => $this->client,
            'cache' => $this->container->get('Cache'),
            'domParser' => $this->container->get('DomParser'),
            'jsonUrlTree' => $this->container->make('JsonUrlTree', array(
                'jsonFilePath' => $this->container->get('Dirs')['tests'] . "/struct.json"
            )),
        ));
        $this->assertFileExists($this->container->get('Dirs')['tests'] . "/struct.json");*/
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
