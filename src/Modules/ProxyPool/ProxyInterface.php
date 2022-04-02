<?php

namespace Letov\Flycatcher\Modules\ProxyPool;

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