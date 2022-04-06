<?php

namespace Letov\Flycatcher\Shell;

use Exception;

class Shell implements ShellInterface
{
    private string $cmd;
    private array $args;
    private string $argDelimiter;

    /**
     * @throws Exception
     */
    public function __construct(string $cmd, string $argDelimiter = " ")
    {
        $cmd = $this->escape($cmd);
        if (empty(trim(shell_exec("command -v $cmd && echo \"ok\"")))) {
            throw new Exception("Command $cmd not found");
        }
        $this->cmd = $cmd;
        $this->argDelimiter = addslashes($argDelimiter);
    }

    private function escape($string): string
    {
        return "\"" . addslashes($string) . "\"";
    }

    public function addArg(string $name, ?string $value = ""): Shell
    {
        if (null !== $value) {
            $this->args[] = [
                $this->escape($name),
                empty($value) ? null : $this->escape($value)
            ];
        }
        return $this;
    }

    public function addArgUnsafe(string $value): Shell
    {
        $this->args[] = [$value, null];
        return $this;
    }

    public function removeFromTail(int $count): Shell
    {
        $totalCount = count($this->args);
        array_splice($this->args, $totalCount - $count, $count);
        return $this;
    }

    public function removeAll(): Shell
    {
        $this->args = [];
        return $this;
    }

    public function run(): ?string
    {
        $cmdWithArgs[] = $this->cmd;
        foreach ($this->args as $arg) {
            $cmdWithArgs[] = empty($arg[1]) ?
                $arg[0] :
                $arg[0] . $this->argDelimiter . $arg[1];
        }
        $cmd = implode(" ", $cmdWithArgs);
        return shell_exec($cmd);
    }
}