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
            if (isset($workload['url']) && isset($workload['filePath']))
            {
                $this->downloadTool->downloadFile($workload['url'], $workload['filePath']);
            }
            return serialize("");
        });
        $this->gearmanWorker->addFunction("updateArgs", function ($job)
        {
            $workload = unserialize($job->workload());
            if (isset($workload['args']))
            {
                $this->downloadTool->updateArgs($workload['args']);
            }
            return serialize("");
        });
        while ($this->gearmanWorker->work()) { }
    }
}