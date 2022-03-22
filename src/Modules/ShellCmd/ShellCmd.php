<?php

namespace Letov\Flycatcher\Modules\ShellCmd;

use Exception;

class ShellCmd implements ShellCmdInterface
{
    private string $cmd;
    private array $args;
    private string $argGlue;

    /**
     * @throws Exception
     */
    public function __construct(string $cmd, string $argGlue = " ")
    {
        $cmd = addslashes($cmd);
        if (empty(trim(shell_exec("command -v \"$cmd\" && echo \"ok\"")))) {
            throw new Exception("Command $cmd not found.");
        }
        $this->cmd = $cmd;
        $this->argGlue = $argGlue;
    }

    public function addArg(string $name, string $value = ""): ShellCmd
    {
        $this->args[$name] = $value;
        return $this;
    }

    public function updateArg(string $name, string $value = ""): ShellCmd
    {
        return $this
            ->removeArg($name)
            ->addArg($name, $value);
    }

    public function removeArg(string $name): ShellCmd
    {
        if (isset($this->args[$name])) {
            unset($this->args[$name]);
        }
        return $this;
    }

    public function run()
    {
        $cmd = addslashes($this->cmd);
        $args = $this->args;
        foreach ($args as $name => $value) {
            $args[$name] = empty($value) ?
                addslashes($name) :
                addslashes($name) . $this->argGlue . addslashes($value);
        }
        $cmdWithArgs = $cmd . " " . implode(" ", $args);
        return shell_exec($cmdWithArgs);
    }
}