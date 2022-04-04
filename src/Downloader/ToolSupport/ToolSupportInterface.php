<?php

namespace Letov\Flycatcher\Downloader\ToolSupport;

use Letov\Flycatcher\Downloader\ArgsSupport\ArgsSupportInterface;

interface ToolSupportInterface
{
    public function __construct(ArgsSupportInterface $argsSupport);
    public function updateArgs(array $args);

}