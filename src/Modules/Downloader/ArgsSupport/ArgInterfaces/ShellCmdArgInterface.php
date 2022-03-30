<?php

namespace Letov\Flycatcher\Modules\Downloader\ArgsSupport\ArgInterfaces;

use Letov\Flycatcher\Modules\ShellCmd\ShellCmdInterface;

interface ShellCmdArgInterface
{
    public function getShellCmd(): ?ShellCmdInterface;
}