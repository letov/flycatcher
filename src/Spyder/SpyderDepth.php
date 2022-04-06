<?php

namespace Letov\Flycatcher\Spyder;

use Exception;
use GearmanClient;
use Letov\Flycatcher\Cache\CacheInterface;

class SpyderDepth
{
    private string $protocol;
    private string $host;
    private array $includePathList;
    private array $excludePathList;

    private string $downloadDir;
    private int $taskParallelLimit;
    private GearmanClient $client;
    private CacheInterface $cache;
    private string $mapFilePath;
    private int $depthOrigLimit;

    /**
     * @throws Exception
     */
    function __construct(
        string         $protocol,
        string         $host,
        string         $rootPath,
        array          $includePathList,
        array          $excludePathList,
        string         $downloadDir,
        int            $depthLimit,
        int            $taskParallelLimit,
        GearmanClient  $client,
        CacheInterface $cache
    )
    {
        $this->protocol = $protocol;
        $this->host = $host;
        $this->downloadDir = $downloadDir;
        $this->taskParallelLimit = $taskParallelLimit;
        $this->client = $client;
        $this->cache = $cache;
        $this->includePathList = $includePathList;
        $this->excludePathList = $excludePathList;
        $this->mapFilePath = "$this->downloadDir/map.txt";
        $this->depthOrigLimit = $depthLimit;
        $this->start($rootPath, $depthLimit);
    }

    /**
     * @throws Exception
     */
    private function start($rootPath, $depthLimit)
    {
        $url = $this->getUrl($rootPath);
        if (null === $url)
        {
            throw new Exception('Root url invalid');
        }
        $filePath = $this->getFilePath($url);
        if (!$this->cache->valid($filePath))
        {
            $this->addToMapFile($url, $depthLimit);
            $this->addDownloadTask($url, $filePath);
            $this->client->runTasks();
            if (!$this->cache->valid($filePath))
            {
                throw new Exception('Root url download error');
            }
        }
        $this->parseFilePathList(array($filePath), $depthLimit);
    }

    private function addToMapFile($url, $depth)
    {
        $padding = str_repeat("   ", $this->depthOrigLimit - $depth);
        @file_put_contents($this->mapFilePath, $padding . $url . PHP_EOL, FILE_APPEND);
    }

    private function getUrl($path): ?string
    {
        if (empty($path) ||
            false !== stripos('#', $path) ||
            filter_var($path, FILTER_VALIDATE_URL))
        {
            return null;
        }
        foreach ($this->includePathList as $includePath)
        {
            if (false === stripos($path, $includePath))
            {
                return null;
            }
        }
        foreach ($this->excludePathList as $excludePath)
        {
            if (false !== stripos($path, $excludePath))
            {
                return null;
            }
        }
        $url = "$this->protocol://$this->host$path";
        if (!filter_var($url, FILTER_VALIDATE_URL))
        {
            return null;
        }
        return $url;
    }

    private function parseFilePathList($filePathList, $depth)
    {
        if ($depth <= 0)
        {
            return;
        }
        while (!empty($filePathList))
        {
            $filePath = array_pop($filePathList);
            $urlList = $this->parseUrls(file_get_contents($filePath));
            $this->downloadPageUrl($urlList, $depth);
        }
    }

    private function downloadPageUrl($urlList, $depth)
    {
        $filePathList = [];
        $taskParallelCount = 0;
        while (!empty($urlList))
        {
            $url = array_pop($urlList);
            $filePath = $this->getFilePath($url);
            $filePathList[] = $filePath;
            if (!$this->cache->valid($filePath))
            {
                $this->addToMapFile($url, $depth);
                $this->addDownloadTask($url, $filePath);
                $taskParallelCount++;
            }
            if ($taskParallelCount >= ($this->taskParallelLimit - 1) || empty($urlList))
            {
                $this->client->runTasks();
                $taskParallelCount = 0;
            }
        }
        $this->parseFilePathList($filePathList, $depth - 1);
    }

    private function getFilePath($url): string
    {
        $fileName = md5($url);
        return "$this->downloadDir/$fileName";
    }

    private function addDownloadTask($url, $filePath)
    {
        $this->client->addTask("download", serialize(array(
            'url' => $url,
            'filePath' => $filePath,
        )));
    }

    private function parseUrls($html): array
    {
        $regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
        $urls = [];
        if (preg_match_all("/$regexp/siU", $html, $matches, PREG_SET_ORDER))
        {
            foreach($matches as $match)
            {
                $url = $this->getUrl($match[2]);
                if (null !== $url)
                {
                    $urls[] = $url;
                }
            }
        }
        $urls = array_unique($urls);
        rsort($urls);
        return $urls;
    }
}