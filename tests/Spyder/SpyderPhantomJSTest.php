<?php

namespace Letov\Flycatcher\Tests\Spyder;

use DI\DependencyException;
use DI\NotFoundException;
use GearmanClient;
use Letov\Flycatcher\Tests\TestCaseContainer;

class SpyderPhantomJSTest extends TestCaseContainer
{
    private GearmanClient $client;

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */

    public function testSpyderPhantomJS()
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
        $this->container->make("SpyderUrlTemplate", array(
            'downloadDir' => $this->container->get('Dirs')['tests'],
            'taskLimit' => $this->container->get("Worker.downloadToolWorker.count"),
            'client' => $this->client,
            'cache' => $this->container->get('Cache'),
            'jsonUrlTree' => $this->container->make('JsonUrlTree', array(
                'jsonFilePath' => $this->container->get('Dirs')['tests'] . "/struct.json"
            )),
            'template' => 'https://www.petshop.ru/catalog/dogs/syxoi/schenki/#pn=%d',
            'range' => range(1,4),
        ));
        $this->assertFileExists($this->container->get('Dirs')['tests'] . "/struct.json");
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    function setWorkers()
    {
        $args = array(
            'CookieFilePath' => $this->tmpCookie,
            'Timeout' => $this->container->get('Downloader.timeoutWithPageContent'),
            'DiskCachePath' => $this->container->get("Dirs")['browsersData'],
            'LocalStoragePath' => $this->container->get("Dirs")['browsersData'],
            'PhantomJSSaveContentPath' => $this->container->get('Dirs')['tests'] . '/save_content',
            'PhantomJSSaveContentWait' => $this->container->get('PhantomJS.connector.pageContentWait'),
            'PhantomJSSaveContentMimeFilter' => array(
                'image/jpeg',
                'image/png',
            ),
            'PhantomJSClickSelectorMap' => array(
                '.slick-slide.slick-active.slick-center.slick-current',
                '.slick-slide.slick-active.slick-current',
                '[data-testid=ProductSlider__up]',
            ),
            'PhantomJSClickSelectorMapRepeat' => 5,
            'PhantomJSConnector' => $this->container->get('PhantomJS.connector.path'),
        );
        for ($i = 0; $i < $this->container->get("Worker.downloadToolWorker.count") * 2; $i++)
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