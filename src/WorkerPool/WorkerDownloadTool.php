<?php

namespace Letov\Flycatcher\WorkerPool;

use GearmanWorker;
use Letov\Flycatcher\Downloader\DownloadToolInterface;

class WorkerDownloadTool implements WorkerInterface
{
    private GearmanWorker $gearmanWorker;
    private string $gearmanHost;
    private int $gearmanPort;
    private DownloadToolInterface $downloadTool;

    public function __construct(GearmanWorker $gearmanWorker, string $gearmanHost, int $gearmanPort, DownloadToolInterface $downloadTool)
    {
        $this->gearmanWorker = $gearmanWorker;
        $this->gearmanHost = $gearmanHost;
        $this->gearmanPort = $gearmanPort;
        $this->downloadTool = $downloadTool;
    }

    function workCycle()
    {
        $this->gearmanWorker->addServer($this->gearmanHost, $this->gearmanPort);
        $this->gearmanWorker->addFunction("download", function ($job)
        {
            $workload = unserialize($job->workload());
            if (!isset($workload['url']) || !isset($workload['filePath']))
            {
                return serialize("");
            }
            $this->downloadTool->downloadFile($workload['url'], $workload['filePath']);
        });
        while ($this->gearmanWorker->work()) { }
    }
}