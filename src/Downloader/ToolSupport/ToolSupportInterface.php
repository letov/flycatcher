<?php

namespace Letov\Flycatcher\Downloader\ToolSupport;

use Letov\Flycatcher\Downloader\ArgsSupport\ArgsSupportInterface;
use Psr\Log\LoggerInterface;

interface ToolSupportInterface
{
    public function __construct(ArgsSupportInterface $argsSupport, ?LoggerInterface $logger = null);

    public function updateArgs(array $args);

}