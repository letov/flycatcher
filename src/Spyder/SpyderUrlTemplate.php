<?php

namespace Letov\Flycatcher\Spyder;

use Exception;
use GearmanClient;
use Letov\Flycatcher\Cache\CacheInterface;

class SpyderUrlTemplate extends SpyderUrlList
{
    function __construct(
        string                $downloadDir,
        int                   $taskLimit,
        GearmanClient         $client,
        CacheInterface        $cache,
        JsonUrlTreeInterface  $jsonUrlTree,
        string                $template,
        array                 $range
    )
    {
        $parseUrl = parse_url($template);
        if (!isset($parseUrl['scheme']) || !isset($parseUrl['host']))
        {
            throw new Exception("Template parse error");
        }
        $urlList = array_map(function ($page)  use ($template) {
            return sprintf($template, $page);
        }, $range);
        parent::__construct($parseUrl['host'], $parseUrl['scheme'], $downloadDir, $taskLimit, $client, $cache, $jsonUrlTree, $urlList);
    }
}