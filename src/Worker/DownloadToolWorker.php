<?php

namespace Letov\Flycatcher\Worker;

use GearmanWorker;
use Letov\Flycatcher\Downloader\DownloadToolInterface;

class DownloadToolWorker implements WorkerInterface
{
    private DownloadToolInterface $downloadTool;

    public function __construct(DownloadToolInterface $downloadTool)
    {
        $this->downloadTool = $downloadTool;
    }

    function work()
    {
        $worker = new GearmanWorker();
        $worker->addServer();
        $worker->addFunction("download", function ($job)
        {
            $workload = unserialize($job->workload());
            if (!isset($workload['url']) || !isset($workload['filePath']))
            {
                return serialize("");
            }
            $this->downloadTool->downloadFile($workload['url'], $workload['filePath']);
        });
        while ($worker->work()) { }
    }
}