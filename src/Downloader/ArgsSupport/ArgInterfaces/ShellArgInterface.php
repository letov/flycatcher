<?php

namespace Letov\Flycatcher\Downloader\ArgsSupport\ArgInterfaces;

use Letov\Flycatcher\Shell\ShellInterface;

interface ShellArgInterface
{
    public function getShell(): ?ShellInterface;
}