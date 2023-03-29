<?php

declare(strict_types=1);

namespace Letov\Flycatcher\Worker;

interface WorkerInterface
{
    public function workCycle();
}
