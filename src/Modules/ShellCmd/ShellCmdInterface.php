<?php

namespace Letov\Flycatcher\Modules\ShellCmd;

interface ShellCmdInterface
{
    public function __construct(string $cmd, string $argDelimiter = " ");
    public function addArg(string $name, ?string $value = ""): ShellCmd;
    public function updateArg(string $name, string $value): ShellCmd;
    public function removeArg(string $name): ShellCmd;
    public function run(): string;
}