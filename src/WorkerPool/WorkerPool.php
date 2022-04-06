<?php

namespace Letov\Flycatcher\WorkerPool;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Exception;

class WorkerPool implements WorkerPoolInterface
{
    protected Container $container;
    protected string $workerName;
    protected string $workerDownloadToolName;
    protected array $workerArgs;
    protected int $workerCount;
    protected int $workerCountCheckDelay;

    protected int $respawnCyclePid;
    protected array $workerPids;

    /**
     * @throws Exception
     */
    public function __construct(Container $container,
                                string $workerName,
                                string $workerDownloadToolName,
                                array $workerArgs,
                                int $workerCount,
                                int $workerCountCheckDelay)
    {
        $this->container = $container;
        $this->workerName = $workerName;
        $this->workerDownloadToolName = $workerDownloadToolName;
        $this->workerArgs = $workerArgs;
        $this->workerCount = $workerCount;
        $this->workerCountCheckDelay = $workerCountCheckDelay;
        $this->workerPids = [];
        $pid = pcntl_fork();
        if (-1 == $pid)
        {
            throw new Exception('Cant create worker pool');
        } elseif ($pid) {
            $this->respawnCyclePid = $pid;
        } else {
            $this->respawnCycle();
            exit;
        }
        sleep(1);
    }

    public function stop()
    {
        posix_kill($this->respawnCyclePid, SIGTERM);
    }

    private function respawnCycleTermHandle()
    {
        declare(ticks = 1);
        pcntl_signal(SIGTERM, function () {
            foreach($this->workerPids as $pid) {
                posix_kill($pid, SIGKILL);
            }
            exit;
        });
        pcntl_signal(SIGINT, function () {
            foreach($this->workerPids as $pid) {
                posix_kill($pid, SIGKILL);
            }
            exit;
        });
    }

    private function respawnCycle()
    {
        $this->respawnCycleTermHandle();
        while (true)
        {
            foreach ($this->workerPids as $pid)
            {
                if (!posix_getpgid($pid)) {
                    unset($this->workerPids[$pid]);
                }
            }
            $workersNeed = $this->workerCount - count($this->workerPids);
            for ($i = 0; $i < $workersNeed; $i++)
            {
                $this->workerStart();
            }
            sleep($this->workerCountCheckDelay);
        }
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     * @throws Exception
     */
    private function workerStart()
    {
        $pid = pcntl_fork();
        if (-1 == $pid)
        {
            throw new Exception('Cant create test worker process');
        } elseif ($pid) {
            $this->workerPids[] = $pid;
        } else {
            $worker = $this->container->make("Worker.$this->workerName",  array(
                "gearmanWorker" => $this->container->get('Gearman.worker'),
                "gearmanHost" => $this->container->get('Gearman.host'),
                "gearmanPort" => $this->container->get('Gearman.port'),
                "downloadTool" => $this->container->make($this->workerDownloadToolName, array(
                    'argsSupport' => $this->container->make('ArgSupport', array(
                        'args' => $this->workerArgs
                    ))
                )),
            ));
            $worker->workCycle();
            exit;
        }
    }
}