<?php

namespace Letov\Flycatcher\Modules\Proxy;

interface ProxyInterface
{
    public function getSocket(): string;
    public function getAuth(): string;
}