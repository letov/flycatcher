<?php

namespace Letov\Flycatcher\Spyder;

use GearmanClient;
use Letov\Flycatcher\Cache\CacheInterface;

abstract class AbstractSpyder
{

    protected string $host;
    protected string $protocol;
    protected string $downloadDir;
    protected int $taskLimit;
    protected GearmanClient $client;
    protected CacheInterface $cache;
    protected JsonUrlTreeInterface $jsonUrlTree;

    public function __construct(
        string                $host,
        string                $protocol,
        string                $downloadDir,
        int                   $taskLimit,
        GearmanClient         $client,
        CacheInterface        $cache,
        JsonUrlTreeInterface $jsonUrlTree
    )
    {
        $this->host = $host;
        $this->protocol = $protocol;
        $this->downloadDir = $downloadDir;
        $this->taskLimit = $taskLimit;
        $this->client = $client;
        $this->cache = $cache;
        $this->jsonUrlTree = $jsonUrlTree;
        $this->jsonUrlTree->setRoot("$this->protocol://$this->host");
    }

    protected function downloadFromUrlList($urlList)
    {
        $taskCount = 0;
        rsort($urlList);
        while (!empty($urlList)) {
            $url = array_pop($urlList);
            $this->jsonUrlTree->add($url);
            $filePath = $this->getFilePath($url);
            if (!$this->cache->valid($filePath)) {
                $this->addDownloadTask($url, $filePath);
                $taskCount++;
            }
            if ($taskCount >= ($this->taskLimit - 1) || empty($urlList)) {
                $this->client->runTasks();
                $taskCount = 0;
            }
        }
    }

    protected function addDownloadTask($url, $filePath)
    {
        $this->client->addTask("download", serialize(array(
            'url' => $url,
            'filePath' => $filePath,
        )));
    }

    protected function getFilePath($url): string
    {
        $fileName = md5($url);
        return "$this->downloadDir/$fileName";
    }
}