<?php

namespace Letov\Flycatcher\Downloader\ToolSupport\Shells;

use Exception;
use Letov\Flycatcher\Downloader\ArgsSupport\ArgsSupportInterface;
use Letov\Flycatcher\Downloader\ToolSupport\ToolSupportInterface;
use Letov\Flycatcher\Shell\ShellInterface;
use Psr\Log\LoggerInterface;

abstract class AbstractShellSupport implements ToolSupportInterface
{

    protected ArgsSupportInterface $argsSupport;
    protected ShellInterface $shell;
    protected ?LoggerInterface $logger;

    /**
     * @throws Exception
     */
    public function __construct(ArgsSupportInterface $argsSupport, ?LoggerInterface $logger = null)
    {
        if (empty($argsSupport->getShell())) {
            throw new Exception("Shell client empty");
        }
        $this->argsSupport = $argsSupport;
        $this->logger = $logger;
        $this->setArgsToShell();
    }

    abstract protected function setArgsToShell();

    public function updateArgs(array $args)
    {
        $this->argsSupport->updateArgs($args);
        $this->setArgsToShell();
    }

    protected function fileNameAddPid(?string $fileName): ?string
    {
        if (null === $fileName) {
            return null;
        }
        $base = pathinfo($fileName);
        $pid = posix_getpid();
        $extension = isset($base['extension']) ? ".{$base['extension']}" : "";
        return "{$base['dirname']}/{$base['filename']}_$pid{$extension}";
    }

    protected function dirAddPid(?string $dirName): ?string
    {
        if (null === $dirName) {
            return null;
        }
        $pid = posix_getpid();
        $newDir = "{$dirName}/$pid";
        if (!file_exists($newDir)) {
            mkdir($newDir);
        }
        return $newDir;
    }
}