<?php

namespace Letov\Flycatcher\Downloader\ToolSupport\Shells;

use Exception;
use Letov\Flycatcher\Downloader\ArgsSupport\ArgsSupportInterface;
use Letov\Flycatcher\Downloader\ToolSupport\ToolSupportInterface;
use Letov\Flycatcher\Shell\ShellInterface;

abstract class AbstractShellSupport implements ToolSupportInterface
{

    protected ArgsSupportInterface $argsSupport;
    protected ShellInterface $shell;

    /**
     * @throws Exception
     */
    public function __construct(ArgsSupportInterface $argsSupport)
    {
        if (empty($argsSupport->getShell())) {
            throw new Exception("Shell command client empty");
        }
        $this->shell = $argsSupport->getShell();
        $this->argsSupport = $argsSupport;
        $this->setArgsToClient();
    }

    abstract protected function setArgsToClient();

    public function updateArgs(array $args)
    {
        $this->argsSupport->updateArgs($args);
        $this->shell->removeAll();
        $this->setArgsToClient();
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