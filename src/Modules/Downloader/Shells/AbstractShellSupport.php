<?php

namespace Letov\Flycatcher\Modules\Downloader\Shells;

use Exception;
use Letov\Flycatcher\Modules\Downloader\ArgsSupport\ArgsSupportInterfacePackage;
use Letov\Flycatcher\Modules\Shell\ShellInterface;

abstract class AbstractShellSupport
{

    protected ArgsSupportInterfacePackage $argsSupport;
    protected ShellInterface $shell;

    /**
     * @throws Exception
     */
    public function __construct(ArgsSupportInterfacePackage $argsSupport)
    {
        if (empty($argsSupport->getShell()))
        {
            throw new Exception("Shell command client empty");
        }
        $this->shell = $argsSupport->getShell();
        $this->argsSupport = $argsSupport;
        $this->setArgsToClient();
    }

    public function updateArgs(array $args)
    {
        $this->argsSupport->updateArgs($args);
        $this->shell->removeAll();
        $this->setArgsToClient();
    }

    abstract protected function setArgsToClient();
}