<?php

namespace Letov\Flycatcher\Worker;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Exception;
use GearmanWorker;
use Letov\Flycatcher\Downloader\DownloadToolInterface;

class WorkerDownloadTool implements WorkerInterface
{
    private Container $container;
    private GearmanWorker $gearmanWorker;
    private DownloadToolInterface $downloadTool;

    /**
     * @throws NotFoundException
     * @throws DependencyException
     */
    public function __construct(
        Container $container
    )
    {
        $this->container = $container;
        $this->gearmanWorker = $this->container->get("Gearman.worker");
        $this->gearmanWorker->addServer($this->container->get("Gearman.host"), $this->container->get("Gearman.port"));
    }

    public function workCycle()
    {
        $this->gearmanWorker->addFunction("setDownloadTool", function ($job) {
            $workload = unserialize($job->workload());
            if (!isset($workload['downloadToolName']) ||
                !isset($workload['shellName']) ||
                !isset($workload['args'])) {
                return serialize("");
            }
            $args = $workload['args'];
            $args['Shell'] =  $this->container->get($workload['shellName']);
            $this->downloadTool = $this->container->make($workload['downloadToolName'], array(
                'argsSupport' => $this->container->make('ArgSupport', array(
                    'args' => $args
                )),
                'logger' => $this->container->get('Logger')
            ));
            return serialize("");
        });
        $this->gearmanWorker->addFunction("updateArgs", function ($job) {
            if (!isset($this->downloadTool))
            {
                throw new Exception("Call setDownloadTool first");
            }
            $workload = unserialize($job->workload());
            if (isset($workload['args'])) {
                $this->downloadTool->updateArgs($workload['args']);
            }
            return serialize("");
        });
        $this->gearmanWorker->addFunction("download", function ($job) {
            if (!isset($this->downloadTool))
            {
                throw new Exception("Call setDownloadTool first");
            }
            $workload = unserialize($job->workload());
            if (isset($workload['url']) && isset($workload['filePath'])) {
                $this->downloadTool->downloadFile($workload['url'], $workload['filePath']);
            }
            return serialize("");
        });
        while ($this->gearmanWorker->work());
    }
}