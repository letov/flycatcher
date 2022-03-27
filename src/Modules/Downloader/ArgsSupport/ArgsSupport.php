<?php

namespace Letov\Flycatcher\Modules\Downloader\ArgsSupport;

class ArgsSupport
{
    protected array $argsStorage;

    public function __construct(array $args)
    {
        $this->argsStorage = $args;
    }

    /**
     * @param string $methodName
     * @return mixed
     */
    protected function getArg(string $methodName)
    {
        $argName = substr($methodName, 3);
        return $this->argsStorage[$argName] ?? null;
    }
}