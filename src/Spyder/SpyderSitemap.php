<?php

namespace Letov\Flycatcher\Spyder;

use Exception;
use GearmanClient;
use Letov\Flycatcher\Cache\CacheInterface;
use Letov\Flycatcher\DomParser\DomDocumentInterface;

class SpyderSitemap extends AbstractSpyder
{
    private DomDocumentInterface $domParser;

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
        DomDocumentInterface  $domParser,
        JsonUrlTreeInterface  $jsonUrlTree
    )
    {
        $this->domParser = $domParser;
        parent::__construct($host, $protocol, $downloadDir, $taskLimit, $client, $cache, $jsonUrlTree);
        $sitemapUrl = $this->getSitemapUrlFromRobots();
        $urlList = $this->parseSitemapUrl($sitemapUrl);
        $this->downloadFromUrlList($urlList);
        $this->jsonUrlTree->save();
    }

    /**
     * @throws Exception
     */
    function parseSitemapUrl($url): array
    {
        $filePath = $this->getFilePath($url);
        if (!$this->cache->valid($filePath))
        {
            $this->downloadFromUrl($url, $filePath);
        }
        if (!@file_exists($filePath))
        {
            throw new Exception("Cant get sitemap");
        }
        $locElementList = $this->domParser
            ->loadFromFile($filePath)
            ->find('loc');
        $locs = [];
        foreach ($locElementList as $locElement)
        {
            $loc = $locElement->innertext();
            $base = pathinfo($loc);
            if (isset($base['extension']) &&
                'xml' === mb_strtolower($base['extension']))
            {
                $subSitemapLinks = $this->parseSitemapUrl($loc);
                if (!empty($subSitemapLinks))
                {
                    $locs = array_merge_recursive($locs, $subSitemapLinks);
                }
            }
            $locs[] = $loc;
        }
        return $locs;
    }

    /**
     * @throws Exception
     */
    function getSitemapUrlFromRobots(): string
    {
        $url = "$this->protocol://$this->host/robots.txt";
        $filePath = $this->getFilePath($url);
        if (!$this->cache->valid($filePath))
        {
            $this->downloadFromUrl($url, $filePath);
        }
        if (!@file_exists($filePath))
        {
            throw new Exception("Cant get robots.txt");
        }
        preg_match("/Sitemap: ([^\r\n]*)/", @file_get_contents($filePath), $match);
        if (count($match) < 2) {
            throw new Exception("No sitemap in robots.txt");
        }
        return trim($match[1]);
    }
}