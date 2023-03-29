<?php

declare(strict_types=1);

namespace Letov\Flycatcher\ProxyPool;

interface ProxyInterface
{
    public function getUser(): string;

    public function getPass(): string;

    public function getIp(): string;

    public function getPort(): string;

    public function getSocket(): string;

    public function getAuth(): string;

    public function getType(): string;
}
