<?php

declare(strict_types=1);

namespace Letov\Flycatcher\Shell;

use Psr\Log\LoggerInterface;

class Shell implements ShellInterface
{
    private string $cmd;
    private array $args;
    private string $argDelimiter;
    private ?LoggerInterface $logger;

    /**
     * @throws \Exception
     */
    public function __construct(string $cmd, ?LoggerInterface $logger = null, string $argDelimiter = ' ')
    {
        $this->logger = $logger;
        $cmd = $this->escape($cmd);
        if (empty(trim(shell_exec("command -v {$cmd} && echo \"ok\"")))) {
            throw new \Exception("Command {$cmd} not found");
        }
        $this->cmd = $cmd;
        $this->argDelimiter = addslashes($argDelimiter);
    }

    public function addArg(string $name, ?string $value = ''): self
    {
        if (null !== $value) {
            $this->args[] = [
                $this->escape($name),
                empty($value) ? null : $this->escape($value),
            ];
        }

        return $this;
    }

    public function addArgUnsafe(string $value): self
    {
        $this->args[] = [$value, null];

        return $this;
    }

    public function removeFromTail(int $count): self
    {
        $totalCount = \count($this->args);
        array_splice($this->args, $totalCount - $count, $count);

        return $this;
    }

    public function removeAll(): self
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
                $arg[0].$this->argDelimiter.$arg[1];
        }
        $cmd = implode(' ', $cmdWithArgs);
        if (!empty($this->logger)) {
            $this->logger->debug($cmd);
        }

        return shell_exec($cmd);
    }

    private function escape($string): string
    {
        return '"'.addslashes($string).'"';
    }
}
