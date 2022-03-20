<?php

namespace Letov\Flycatcher\Modules\ShellCmd;

class ShellCmd implements ShellCmdInterface
{
    private string $args;

    public function __construct()
    {
        $this->args = "";
    }

    public function addFlag(string $flag): ShellCmd
    {
        $flag = addslashes($flag);
        $this->args .= " \"{$flag}\"";
        return $this;
    }

    public function addArg(string $name, string $value, string $glue = " "): ShellCmd
    {
        $name = addslashes($name);
        $value = addslashes($value);
        $glue = addslashes($glue);
        $this->args .= " \"{$name}\"{$glue}\"{$value}\"";
        return $this;
    }

    public function run($cmd)
    {
        $cmd = addslashes($cmd);
        return shell_exec("{$cmd} {$this->args}");
    }
}