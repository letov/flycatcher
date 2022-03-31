<?php

namespace Letov\Flycatcher\Modules\Shell;

interface ShellInterface
{
    public function __construct(string $cmd, string $argDelimiter = " ");
    public function addArg(string $name, string $value = ""): Shell;
    public function addArgUnsafe(string $value): Shell;
    public function removeFromTail(int $count): Shell;
    public function removeAll(): Shell;
    public function run(): ?string;
}