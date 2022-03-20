<?php

namespace Letov\Flycatcher\Modules\Proxy;

class Proxy implements ProxyInterface
{
    public String $ip;
    public String $port;
    public String $user;
    public String $pass;

    public function __construct($ip, $port, $user, $pass)
    {
        $this->ip = $ip;
        $this->port = $port;
        $this->user = $user;
        $this->pass = $pass;
    }

    public function getSocket(): string
    {
        return "$this->ip:$this->port";
    }

    public function getAuth(): string
    {
        return "$this->user:$this->pass";
    }
}