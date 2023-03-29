<?php

declare(strict_types=1);

namespace Letov\Flycatcher\Shell;

use Psr\Log\LoggerInterface;

interface ShellInterface
{
    public function __construct(string $cmd, ?LoggerInterface $logger = null, string $argDelimiter = ' ');

    public function addArg(string $name, string $value = ''): Shell;

    public function addArgUnsafe(string $value): Shell;

    public function removeFromTail(int $count): Shell;

    public function removeAll(): Shell;

    public function run(): ?string;
}
