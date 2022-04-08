<?php

namespace Letov\Flycatcher\Spyder;

use Exception;
use GearmanClient;
use Letov\Flycatcher\Cache\CacheInterface;

class SpyderDepth extends AbstractSpyder
{
    private array $includePathList;
    private array $excludePathList;

    /**
     * @throws Exception
     */
    function __construct(
        string                $host,
        string                $protocol,
        string                $downloadDir,
        int                   $taskLimit,
        GearmanClient         $client,
        CacheInterface        $cache,
        JsonUrlTreeInterface  $jsonUrlTree,
        string                $rootPath,
        int                   $depthLimit,
        array                 $includePathList = [],
        array                 $excludePathList = []
    )
    {
        parent::__construct($host, $protocol, $downloadDir, $taskLimit, $client, $cache, $jsonUrlTree);
        $this->includePathList = $includePathList;
        $this->excludePathList = $excludePathList;
        $this->start($rootPath, $depthLimit);
        $this->jsonUrlTree->save();
    }

    /**
     * @throws Exception
     */
    private function start($rootPath, $depth)
    {
        $url = "$this->protocol://$this->host$rootPath";
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new Exception('Root url invalid');
        }
        $this->jsonUrlTree->add($url);
        $filePath = $this->getFilePath($url);
        if (!$this->cache->valid($filePath)) {
            $this->addDownloadTask($url, $filePath);
            $this->client->runTasks();
            if (!$this->cache->valid($filePath)) {
                throw new Exception('Root url download error');
            }
        }
        $this->iterateUrlList(array($url), $depth);
    }

    private function iterateUrlList($urlList, $depth)
    {
        if ($depth <= 0) {
            return;
        }
        while (!empty($urlList)) {
            $parentUrl = array_pop($urlList);
            $filePath = $this->getFilePath($parentUrl);
            if (file_exists($filePath)) {
                $html = file_get_contents($filePath);
                $childrenUrlList = $this->getUrlListFromHTML($html);
                $this->downloadFromUrlListDepth($childrenUrlList, $depth);
            }
        }
    }

    private function getUrlListFromHTML($html): array
    {
        $regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
        $urls = [];
        if (preg_match_all("/$regexp/siU", $html, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $url = $this->getUrl($match[2]);
                if (null !== $url) {
                    $urls[] = $url;
                }
            }
        }
        $urls = array_unique($urls);
        return $urls;
    }

    private function getUrl($path): ?string
    {
        if (!$this->validatePath($path)) {
            return null;
        }
        $url = "$this->protocol://$this->host$path";
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }
        return $url;
    }

    private function validatePath($path): bool
    {
        if (empty($path) /*|| false !== stripos('#', $path)*/) {
            return false;
        }
        foreach ($this->includePathList as $includePath) {
            if (false !== stripos($path, $includePath)) {
                return true;
            }
        }
        if (!empty($this->includePathList)) {
            return false;
        }
        foreach ($this->excludePathList as $excludePath) {
            if (false !== stripos($path, $excludePath)) {
                return false;
            }
        }
        return true;
    }

    private function downloadFromUrlListDepth($childrenUrlList, $depth)
    {
        $this->downloadFromUrlList($childrenUrlList);
        $this->iterateUrlList($childrenUrlList, $depth - 1);
    }
}