<?php

namespace Letov\Flycatcher\Modules\Downloader\ArgsSupport\ArgInterfaces;

use Letov\Flycatcher\Modules\Shell\ShellInterface;

interface ShellArgInterface
{
    public function getShell(): ?ShellInterface;
}