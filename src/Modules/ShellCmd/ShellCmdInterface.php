<?php

namespace Letov\Flycatcher\Modules\ShellCmd;

interface ShellCmdInterface
{
    public function addFlag(string $flag);
    public function addArg(string $name, string $value, string $glue = " ");
    public function run($cmd);
}