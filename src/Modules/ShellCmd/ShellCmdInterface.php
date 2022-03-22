<?php

namespace Letov\Flycatcher\Modules\ShellCmd;

interface ShellCmdInterface
{
    public function addArg(string $name, string $value = ""): ShellCmd;
    public function updateArg(string $name, string $value = ""): ShellCmd;
    public function removeArg(string $name): ShellCmd;
    public function run();
}