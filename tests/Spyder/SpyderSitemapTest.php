<?php

declare(strict_types=1);

namespace Letov\Flycatcher\Tests\Spyder;

use DI\DependencyException;
use DI\NotFoundException;
use Letov\Flycatcher\Tests\TestCaseContainer;

/**
 * @internal
 *
 * @coversNothing
 */
final class SpyderSitemapTest extends TestCaseContainer
{
    private \GearmanClient $client;

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testSpyderSitemap(): void
    {
        $this->client = $this->container->get('Gearman.client');
        $this->client->addServer(
            $this->container->get('Gearman.host'),
            $this->container->get('Gearman.port')
        );
        $this->client->setCompleteCallback(function ($task): void {
            echo sprintf("complete task %s %s\n", $task->jobHandle(), $task->functionName());
        });
        $this->client->setTimeout($this->container->get('Downloader.timeoutWithCaptcha') * 1000 * 2);
        $this->setWorkers();
        $this->container->make('Spyder.sitemap', [
            'host' => 'someurl.ru',
            'protocol' => 'https',
            'downloadDir' => $this->container->get('Directories.paths')['download'],
            'taskLimit' => $this->container->get('Worker.downloadTool.count'),
            'client' => $this->client,
            'cache' => $this->container->get('Cache'),
            'domParser' => $this->container->get('DomParser'),
            'jsonUrlTree' => $this->container->make('JsonUrlTree', [
                'jsonFilePath' => $this->container->get('Directories.paths')['download'].'/struct.json',
            ]),
        ]);
        // $this->assertFileExists($this->container->get('Directories.paths')['download'] . "/struct.json");
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function setWorkers(): void
    {
        $args = [
            'CookieFilePath' => $this->tmpCookie,
            'Timeout' => $this->container->get('Downloader.timeout'),
        ];
        for ($i = 0; $i < $this->container->get('Worker.downloadTool.count') * 2; ++$i) {
            $this->client->addTask('setDownloadTool', serialize([
                'downloadToolName' => 'Wget',
                'shellName' => 'Wget.shell',
                'args' => $args,
            ]));
        }
        $this->client->runTasks();
    }
}
