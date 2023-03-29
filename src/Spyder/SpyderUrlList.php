<?php

declare(strict_types=1);

namespace Letov\Flycatcher\Spyder;

use Letov\Flycatcher\Cache\CacheInterface;

class SpyderUrlList extends AbstractSpyder
{
    /**
     * @throws \Exception
     */
    public function __construct(
        string $host,
        string $protocol,
        string $downloadDir,
        int $taskLimit,
        \GearmanClient $client,
        CacheInterface $cache,
        JsonUrlTreeInterface $jsonUrlTree,
        array $urlList
    ) {
        parent::__construct($host, $protocol, $downloadDir, $taskLimit, $client, $cache, $jsonUrlTree);
        $this->downloadFromUrlList($urlList);
        $this->jsonUrlTree->save();
    }
}
