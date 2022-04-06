<?php

namespace Letov\Flycatcher\Tests\Spyder;

use DI\DependencyException;
use DI\NotFoundException;
use Letov\Flycatcher\Tests\TestCaseContainer;
use Letov\Flycatcher\WorkerPool\WorkerPoolInterface;

class SpyderDepthTest extends TestCaseContainer
{
    private WorkerPoolInterface $workerPool;
    private int $workerCount;

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testSpyderDepth()
    {
        $this->workerCount = 15;
        @unlink("{$this->container->get('Dirs')['download']}/map.txt");
        $this->createPhantomWorkerPool();
        $client = $this->container->get('Gearman.client');
        $client->addServer(
            $this->container->get('Gearman.host'),
            $this->container->get('Gearman.port')
        );
        $client->setTimeout($this->container->get("Downloader.timeout") * 1000);
        $this->container->make("SpyderDepth", array(
            'protocol' => 'https',
            'host' => 'www.petshop.ru',
            'rootPath' => '/catalog/',
            'includePathList' => array(
                '/catalog'
            ),
            'excludePathList' => array(
                '/brand'
            ),
            'downloadDir' => $this->container->get('Dirs')['download'],
            'depthLimit' => 1,
            'taskParallelLimit' => $this->workerCount,
            'client' => $client,
            'cache' => $this->container->get('Cache')
        ));
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
            'Timeout' => $this->container->get('Downloader.timeout'),
            'DiskCachePath' => $this->container->get("Dirs")['browsersData'],
            'LocalStoragePath' => $this->container->get("Dirs")['browsersData'],
            'Shell' => $this->container->get("Curl.shell")
        );
        $this->workerPool = $this->container->make("Worker.pool", array(
            'container' => $this->container,
            'workerName' => 'downloadToolWorker',
            'workerDownloadToolName' => 'Curl',
            'workerArgs' => $args,
            'workerCount' => $this->workerCount,
            'workerCountCheckDelay' => $this->container->get('Worker.countCheckDelay'),
        ));
    }
}
