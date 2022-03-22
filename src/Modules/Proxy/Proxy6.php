<?php

namespace Letov\Flycatcher\Modules\Proxy;

class Proxy6 implements ProxyInterface
{
    private object $proxy;

    public function __construct(object $proxy)
    {
        $this->proxy = $proxy;
    }

    public function getSocket(): string
    {
        return "{$this->proxy->ip}:{$this->proxy->port}";
    }

    public function getAuth(): string
    {
        return "{$this->proxy->user}:{$this->proxy->pass}";
    }
}