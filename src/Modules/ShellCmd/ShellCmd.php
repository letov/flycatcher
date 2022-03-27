<?php

namespace Letov\Flycatcher\Modules\ShellCmd;

use Exception;

class ShellCmd implements ShellCmdInterface
{
    private string $cmd;
    private array $args;
    private string $argDelimiter;

    /**
     * @throws Exception
     */
    public function __construct(string $cmd, string $argDelimiter = " ")
    {
        $cmd = addslashes($cmd);
        if (empty(trim(shell_exec("command -v \"$cmd\" && echo \"ok\"")))) {
            throw new Exception("Command $cmd not found");
        }
        $this->cmd = $cmd;
        $this->argDelimiter = $argDelimiter;
    }

    public function addArg(string $name, ?string $value = ""): ShellCmd
    {
        if (null !== $value) {
            $this->args[] = [addslashes($name), empty($value) ? $value : addslashes($value)];
        }
        return $this;
    }

    public function updateArg(string $name, string $value): ShellCmd
    {
        foreach ($this->args as $key => $arg) {
            if (addslashes($name) == addslashes($arg[0])) {
                $this->args[$key][1] = addslashes($value);
            }
        }
        return $this;
    }

    public function addUnsafeSuffix($value): ShellCmd
    {
        $this->args[] = [$value, null];
        return $this;
    }

    public function removeArg(string $name): ShellCmd
    {
        foreach ($this->args as $key => $arg) {
            if (addslashes($name) == addslashes($arg[0])) {
                unset($this->args[$key]);
            }
        }
        return $this;
    }

    public function run(): string
    {
        $cmdWithArgs[] = $this->cmd;
        foreach ($this->args as $arg) {
            $cmdWithArgs[] = empty($arg[1]) ?
                $arg[0] :
                $arg[0] . $this->argDelimiter . "\"" . $arg[1] . "\"";
        }
        $cmd = implode(" ", $cmdWithArgs);
        return shell_exec($cmd);
    }
}