<?php

namespace Letov\Flycatcher\Modules\Proxy\Proxy6Service;

use Letov\Flycatcher\Modules\Proxy\ProxyInterface;

class Proxy6 implements ProxyInterface
{
    private object $proxy;

    public function __construct(object $proxy)
    {
        $this->proxy = $proxy;
    }

    public function getUser(): string
    {
        return $this->proxy->user;
    }

    public function getPass(): string
    {
        return $this->proxy->pass;
    }

    public function getIp(): string
    {
        return $this->proxy->ip;
    }

    public function getPort(): string
    {
        return $this->proxy->port;
    }

    public function getSocket(): string
    {
        return "{$this->proxy->ip}:{$this->proxy->port}";
    }

    public function getAuth(): string
    {
        return "{$this->proxy->user}:{$this->proxy->pass}";
    }

    public function getType(): string
    {
        return $this->proxy->type == 'http' ? 'https' : 'socks5';
    }
}