<?php

namespace Letov\Flycatcher\Modules\ShellCmd;

interface ShellCmdInterface
{
    public function __construct(string $cmd, string $argDelimiter = " ");
    public function addArg(string $name, string $value = ""): ShellCmd;
    public function addArgUnsafe(string $value): ShellCmd;
    public function removeFromTail(int $count): ShellCmd;
    public function removeAll(): ShellCmd;
    public function run(): ?string;
}